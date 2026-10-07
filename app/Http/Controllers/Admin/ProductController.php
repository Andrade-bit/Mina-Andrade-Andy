<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pos\TerminalController;
use App\Models\CupSize;
use App\Models\Ingredient;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCupSize;
use App\Models\ProductIngredientSize;
use App\Models\SalesTransactionItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * List every menu item with its category, filterable by name, category,
     * and sales performance (top sellers / slow movers / never sold).
     */
    public function index(Request $request): View
    {
        $query = Product::with('productCategory')->withSum([
            'salesTransactionItems as sold_today' => fn ($q) => $q->whereHas('salesTransaction', fn ($t) => $t->whereDate('transaction_date', today())->where('status', '!=', 'voided')),
            'salesTransactionItems as sold_total' => fn ($q) => $q->whereHas('salesTransaction', fn ($t) => $t->where('status', '!=', 'voided')),
        ], 'quantity');

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('category')) {
            $query->where('product_category_id', $request->integer('category'));
        }

        $unavailable = Product::unavailableNow();

        if ($request->query('availability') === 'unavailable') {
            $query->whereIn('id', $unavailable->keys());
        }

        $soldCounts = [];

        if ($request->filled('performance')) {
            [$matchingIds, $soldCounts] = $this->performanceFilterIds($request->string('performance')->toString());
            $query->whereIn('id', $matchingIds);
        }

        $products = $query->orderBy('product_name')->paginate(12)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => ProductCategory::orderBy('category_name')->get(),
            'soldCounts' => $soldCounts,
            'unavailable' => $unavailable,
        ]);
    }

    /**
     * A product is only worth selling if the system knows what it uses up, so saving needs at least one
     * ingredient with an amount.
     *
     * @param  array<int, array<int, mixed>>  $ingredients  [inventory_item_id => [cup_size_id => amount]]
     *
     * @throws ValidationException
     */
    private function requireIngredients(array $ingredients): void
    {
        $hasOne = collect($ingredients)->flatten()->contains(fn ($amount) => is_numeric($amount) && (float) $amount > 0);

        if (! $hasOne) {
            throw ValidationException::withMessages([
                'ingredients' => 'Add at least one ingredient, with an amount above 0, before saving this product.',
            ]);
        }
    }

    /**
     * Resolve which product IDs match a performance filter, using the same
     * 30-day, below-average rule as the Dashboard's Top Sellers / Slow Movers.
     *
     * @return array{0: Collection<int, int>, 1: array<int, int>}
     */
    private function performanceFilterIds(string $performance): array
    {
        $periodStart = now()->subDays(30);

        $soldByProduct = SalesTransactionItem::whereHas('salesTransaction', function ($q) use ($periodStart) {
            $q->where('status', '!=', 'voided')->where('transaction_date', '>=', $periodStart);
        })->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->pluck('total_qty', 'product_id');

        if ($performance === 'never') {
            $everSoldIds = SalesTransactionItem::whereHas('salesTransaction', fn ($q) => $q->where('status', '!=', 'voided'))
                ->distinct()
                ->pluck('product_id');

            return [Product::whereNotIn('id', $everSoldIds)->pluck('id'), []];
        }

        $allProductIds = Product::pluck('id');
        $average = $allProductIds->isEmpty() ? 0 : $allProductIds->sum(fn ($id) => (int) ($soldByProduct[$id] ?? 0)) / $allProductIds->count();

        $matchingIds = $allProductIds->filter(function ($id) use ($performance, $soldByProduct, $average) {
            $sold = (int) ($soldByProduct[$id] ?? 0);

            return $performance === 'top' ? $sold > $average : $sold < $average;
        })->values();

        return [$matchingIds, $soldByProduct->map(fn ($v) => (int) $v)->toArray()];
    }

    /**
     * Show the form for adding a new menu item.
     */
    public function create(): View
    {
        $categories = ProductCategory::orderBy('category_name')->get();
        $cupSizes = CupSize::orderBy('price')->get();
        $inventoryItems = InventoryItem::where('type', 'ingredient')->orderBy('name')->get();

        return view('admin.products.create', [
            'categories' => $categories,
            'cupSizes' => $cupSizes,
            'inventoryItems' => $inventoryItems,
            'productIngredients' => collect(),
            'sizeAmounts' => [],
        ]);
    }

    /**
     * Add a new menu item, including optional per-size availability and
     * price overrides (sizes[{cup_size_id}][is_available|price]) and the
     * ingredients it consumes per unit sold (ingredients[{inventory_item_id}]).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.is_available' => ['nullable', 'boolean'],
            'sizes.*.price' => ['nullable', 'numeric', 'min:0'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*' => ['nullable', 'array'],
            'ingredients.*.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->requireIngredients($validated['ingredients'] ?? []);

        $product = Product::create([
            'product_category_id' => $validated['product_category_id'],
            'product_name' => $validated['product_name'],
            'image' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null,
        ]);

        foreach ($validated['sizes'] ?? [] as $cupSizeId => $size) {
            ProductCupSize::updateOrCreate(
                ['product_id' => $product->id, 'cup_size_id' => $cupSizeId],
                [
                    'is_available' => $request->boolean("sizes.$cupSizeId.is_available"),
                    'price' => $size['price'] !== '' && $size['price'] !== null ? $size['price'] : null,
                ]
            );
        }

        $this->syncIngredients($product, $validated['ingredients'] ?? []);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    /**
     * Save a product's ingredients from ingredients[inventory_item_id][cup_size_id] = amount, skipping blank
     * amounts. Each typed amount is kept for its cup size; the ingredient's base amount (the fallback for a
     * size left blank) is the recipe size's amount, or the smallest one typed.
     *
     * @param  array<int, array<int, mixed>>  $ingredients
     */
    private function syncIngredients(Product $product, array $ingredients): void
    {
        $cupSizeIds = CupSize::pluck('id')->all();
        $recipeSizeId = CupSize::recipeSize()?->id;
        $base = [];
        $sizeRows = [];

        foreach ($ingredients as $inventoryItemId => $bySize) {
            $typed = collect($bySize)->filter(fn ($amount, $cupSizeId) => in_array((int) $cupSizeId, $cupSizeIds, true) && is_numeric($amount) && (float) $amount > 0);

            if ($typed->isEmpty()) {
                continue;
            }

            $ingredient = Ingredient::firstOrCreate(['inventory_item_id' => $inventoryItemId]);
            $base[$ingredient->id] = ['quantity_required' => $typed->get($recipeSizeId) ?? $typed->min()];

            foreach ($typed as $cupSizeId => $amount) {
                $sizeRows[] = [
                    'product_id' => $product->id,
                    'ingredient_id' => $ingredient->id,
                    'cup_size_id' => (int) $cupSizeId,
                    'quantity_required' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::transaction(function () use ($product, $base, $sizeRows) {
            $product->ingredients()->sync($base);
            $product->ingredientSizeAmounts()->delete();
            ProductIngredientSize::insert($sizeRows);
        });
    }

    /**
     * What to show in the ingredient grid: the amount typed for each size. A product saved before sizes had
     * their own amounts has one base amount, shown under the recipe size (or under every size when none is set).
     *
     * @param  Collection<int, Ingredient>  $productIngredients  keyed by inventory item id
     * @param  Collection<int, CupSize>  $cupSizes
     * @return array<int, array<int, string>> [inventory_item_id => [cup_size_id => amount]]
     */
    private function sizeAmountsFor(Product $product, Collection $productIngredients, Collection $cupSizes): array
    {
        $recipeSize = $cupSizes->firstWhere('is_recipe_size', true);
        $typed = $product->ingredientSizeAmounts()->get()->groupBy('ingredient_id');
        $amounts = [];

        foreach ($productIngredients as $inventoryItemId => $ingredient) {
            if ($typed->has($ingredient->id)) {
                foreach ($typed[$ingredient->id] as $row) {
                    $amounts[$inventoryItemId][$row->cup_size_id] = $row->quantity_required;
                }

                continue;
            }

            foreach ($cupSizes as $size) {
                if (! $recipeSize || $size->id === $recipeSize->id) {
                    $amounts[$inventoryItemId][$size->id] = $ingredient->pivot->quantity_required;
                }
            }
        }

        return $amounts;
    }

    /**
     * Show the form for editing a menu item.
     */
    public function edit(Product $product): View
    {
        $categories = ProductCategory::orderBy('category_name')->get();
        $cupSizes = CupSize::orderBy('price')->get();
        $overrides = $product->cupSizePrices()->get()->keyBy('cup_size_id');
        $inventoryItems = InventoryItem::where('type', 'ingredient')->orderBy('name')->get();
        $productIngredients = $product->ingredients()->get()->keyBy('inventory_item_id');
        $sizeAmounts = $this->sizeAmountsFor($product, $productIngredients, $cupSizes);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'cupSizes' => $cupSizes,
            'overrides' => $overrides,
            'inventoryItems' => $inventoryItems,
            'productIngredients' => $productIngredients,
            'sizeAmounts' => $sizeAmounts,
        ]);
    }

    /**
     * Update a menu item, including per-size availability and price overrides
     * and the ingredients it consumes per unit sold.
     *
     * Expects optional sizes[{cup_size_id}][is_available] and
     * sizes[{cup_size_id}][price] for each cup size, and
     * ingredients[{inventory_item_id}] = quantity required per sale.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.is_available' => ['nullable', 'boolean'],
            'sizes.*.price' => ['nullable', 'numeric', 'min:0'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*' => ['nullable', 'array'],
            'ingredients.*.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->requireIngredients($validated['ingredients'] ?? []);

        $imagePath = $product->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $product->update([
            'product_category_id' => $validated['product_category_id'],
            'product_name' => $validated['product_name'],
            'image' => $imagePath,
        ]);

        foreach ($validated['sizes'] ?? [] as $cupSizeId => $size) {
            ProductCupSize::updateOrCreate(
                ['product_id' => $product->id, 'cup_size_id' => $cupSizeId],
                [
                    'is_available' => $request->boolean("sizes.$cupSizeId.is_available"),
                    'price' => $size['price'] !== '' && $size['price'] !== null ? $size['price'] : null,
                ]
            );
        }

        $this->syncIngredients($product, $validated['ingredients'] ?? []);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    /**
     * Archive a menu item (soft delete) — it leaves the POS and menu but
     * stays in sales history and can be restored.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $holders = TerminalController::openCartsHolding([$product->id]);

        if ($holders->isNotEmpty()) {
            return redirect()->route('admin.products.index')->with('error', TerminalController::openCartMessage($product->product_name, $holders));
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product archived.');
    }

    /**
     * Bring an archived menu item back.
     */
    public function restore(int $product): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($product)->restore();

        return redirect()->route('admin.products.index', ['archived' => 1])->with('status', 'Product restored.');
    }
}

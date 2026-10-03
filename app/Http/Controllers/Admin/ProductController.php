<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CupSize;
use App\Models\Ingredient;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCupSize;
use App\Models\SalesTransactionItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * List every menu item with its category, filterable by name, category,
     * and sales performance (top sellers / slow movers / never sold).
     */
    public function index(Request $request): View
    {
        $query = Product::with('productCategory');

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('category')) {
            $query->where('product_category_id', $request->integer('category'));
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
        ]);
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
            'ingredients.*' => ['nullable', 'numeric', 'min:0'],
        ]);

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
     * Attach/update/detach a product's ingredients from a flat
     * [inventory_item_id => quantity_required] map, skipping blank quantities.
     *
     * @param  array<int, mixed>  $ingredients
     */
    private function syncIngredients(Product $product, array $ingredients): void
    {
        $syncData = [];

        foreach ($ingredients as $inventoryItemId => $quantity) {
            if ($quantity === null || $quantity === '' || (float) $quantity <= 0) {
                continue;
            }

            $ingredient = Ingredient::firstOrCreate(['inventory_item_id' => $inventoryItemId]);
            $syncData[$ingredient->id] = ['quantity_required' => $quantity];
        }

        $product->ingredients()->sync($syncData);
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

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'cupSizes' => $cupSizes,
            'overrides' => $overrides,
            'inventoryItems' => $inventoryItems,
            'productIngredients' => $productIngredients,
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
            'ingredients.*' => ['nullable', 'numeric', 'min:0'],
        ]);

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
     * Remove a menu item.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product removed.');
    }
}

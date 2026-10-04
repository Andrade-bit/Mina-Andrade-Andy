<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\CupSize;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Promo;
use App\Models\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    /**
     * Show the POS terminal for whoever unlocked it with their passcode.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $credentialId = $request->session()->get('pos_credential_id');

        if (! $credentialId) {
            return redirect()->route('pos.login');
        }

        $credential = Credential::findOrFail($credentialId);

        $imagesByKeyword = [
            'blue lemonade' => 'blue-lemonade.jpg',
            'lemonade' => 'lemonade.jpg',
            'americano' => 'americano.jpg',
            'cafe latte' => 'cafe-latte.jpg',
            'cappuccino' => 'cappuccino.jpg',
            'spanish latte' => 'spanish-latte.jpg',
            'caramel macchiato' => 'caramel-macchiato.jpg',
            'matcha' => 'matcha-latte.jpg',
            'chocolate' => 'chocolate.jpg',
            'strawberry' => 'strawberry-milk.jpg',
            'mango' => 'mango-juice.jpg',
        ];

        $products = Product::with('productCategory', 'cupSizePrices')->orderBy('product_name')->get()->each(function (Product $product) use ($imagesByKeyword) {
            if ($product->image) {
                $product->display_image = $product->imageUrl();
            } else {
                $name = strtolower($product->product_name);
                $match = collect($imagesByKeyword)->first(fn ($file, $keyword) => str_contains($name, $keyword));
                $product->display_image = asset('images/products/'.($match ?? 'placeholder.jpg'));
            }
            $product->sizes = $product->effectiveCupSizes();
        });

        return view('pos.terminal', [
            'credential' => $credential,
            'products' => $products,
            'cupSizes' => CupSize::orderBy('price')->get(),
        ]);
    }

    /**
     * Process a sale: create the SalesTransaction + line items, and deduct
     * ingredients and cup stock straight from inventory.
     *
     * Expects: payment_method, items[] = [{product_id, cup_size_id, quantity, price_at_order}, ...]
     */
    public function store(Request $request): JsonResponse
    {
        $credentialId = $request->session()->get('pos_credential_id');

        if (! $credentialId) {
            return response()->json(['message' => 'Your session expired. Please log in again.'], 401);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.cup_size_id' => ['required', 'exists:cup_sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price_at_order' => ['required', 'numeric', 'min:0'],
        ]);

        $promo = null;
        if (! empty($validated['promo_code'])) {
            $promo = Promo::where('code', strtoupper($validated['promo_code']))->first();

            if (! $promo || ! $promo->isValid()) {
                return response()->json(['message' => 'That promo code is invalid or expired.'], 422);
            }
        }

        $transaction = DB::transaction(function () use ($validated, $credentialId, $promo) {
            $itemsTotal = 0;

            foreach ($validated['items'] as $item) {
                $itemsTotal += $item['quantity'] * $item['price_at_order'];
            }

            $discountAmount = $promo ? $promo->discountFor($itemsTotal) : 0;

            $sale = SalesTransaction::create([
                'transaction_date' => now(),
                'payment_method' => $validated['payment_method'],
                'total_amount' => $itemsTotal - $discountAmount,
                'credential_id' => $credentialId,
                'status' => 'completed',
                'promo_id' => $promo?->id,
                'discount_amount' => $discountAmount,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::withTrashed()->with('ingredients.inventoryItem')->findOrFail($item['product_id']);
                $cupSize = CupSize::withTrashed()->findOrFail($item['cup_size_id']);
                $subtotal = $item['quantity'] * $item['price_at_order'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'cup_size_id' => $cupSize->id,
                    'quantity' => $item['quantity'],
                    'price_at_order' => $item['price_at_order'],
                    'subtotal' => $subtotal,
                ]);

                // Deduct each ingredient this product needs, scaled by quantity sold.
                foreach ($product->ingredients as $ingredient) {
                    $inventoryItem = $ingredient->inventoryItem;

                    if (! $inventoryItem) {
                        continue;
                    }

                    $consumed = $ingredient->pivot->quantity_required * $item['quantity'];

                    $inventoryItem = $inventoryItem->newQuery()->lockForUpdate()->find($inventoryItem->id);
                    $inventoryItem->decrement('current_quantity', $consumed);

                    InventoryTransaction::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'transaction_type' => 'Sales',
                        'quantity' => $consumed,
                        'inventory_transaction_date' => now()->toDateString(),
                        'reason' => 'Sale #'.$sale->id,
                    ]);
                }

                // Deduct the cup stock for this cup size, if it is tracked in inventory.
                if ($cupSize->inventory_item_id) {
                    $cupStock = $cupSize->inventoryItem()->lockForUpdate()->first();
                    $cupStock->decrement('current_quantity', $item['quantity']);

                    InventoryTransaction::create([
                        'inventory_item_id' => $cupStock->id,
                        'transaction_type' => 'Sales',
                        'quantity' => $item['quantity'],
                        'inventory_transaction_date' => now()->toDateString(),
                        'reason' => 'Sale #'.$sale->id,
                    ]);
                }
            }

            return $sale;
        });

        return response()->json([
            'message' => 'Sale recorded.',
            'transaction' => $transaction->load('items.product', 'items.cupSize', 'promo'),
        ], 201);
    }

    /**
     * A POS terminal reports which products are in its open (not yet charged)
     * cart so the admin side can refuse to archive them mid-order. An empty
     * list clears the terminal's cart. Entries expire on their own if the
     * terminal goes quiet.
     */
    public function syncCart(Request $request): JsonResponse
    {
        $credentialId = $request->session()->get('pos_credential_id');

        if (! $credentialId) {
            return response()->json(['ok' => false], 401);
        }

        $productIds = collect($request->input('product_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $key = 'pos_cart:'.$request->session()->getId();
        $index = Cache::get('pos_cart_index', []);

        if ($productIds === []) {
            Cache::forget($key);
            $index = array_values(array_diff($index, [$key]));
        } else {
            $credential = Credential::find($credentialId);

            Cache::put($key, [
                'name' => trim(($credential->first_name ?? '').' '.($credential->last_name ?? '')),
                'product_ids' => $productIds,
                'at' => now()->timestamp,
            ], now()->addMinutes(5));

            if (! in_array($key, $index, true)) {
                $index[] = $key;
            }
        }

        Cache::put('pos_cart_index', $index, now()->addDay());

        return response()->json(['ok' => true]);
    }

    /**
     * The open POS carts that currently contain any of the given products.
     *
     * @param  array<int, int>  $productIds
     * @return Collection<int, array{name: string, product_ids: array<int, int>, at: int}>
     */
    public static function openCartsHolding(array $productIds): Collection
    {
        $liveKeys = [];
        $holders = collect();

        foreach (Cache::get('pos_cart_index', []) as $key) {
            $cart = Cache::get($key);

            if (! $cart) {
                continue;
            }

            $liveKeys[] = $key;

            if (array_intersect($productIds, $cart['product_ids'])) {
                $holders->push($cart);
            }
        }

        Cache::put('pos_cart_index', $liveKeys, now()->addDay());

        return $holders;
    }

    /**
     * A plain-language reason an archive was refused because of open carts.
     *
     * @param  Collection<int, array{name: string, product_ids: array<int, int>, at: int}>  $holders
     */
    public static function openCartMessage(string $subject, Collection $holders): string
    {
        $who = $holders->map(fn ($cart) => ($cart['name'] ?: 'a cashier').' ('.Carbon::createFromTimestamp($cart['at'])->diffForHumans().')')->unique()->implode(', ');

        return "Can't archive {$subject} right now. It's in an open order on a POS terminal: {$who}. Wait until the order is charged or cleared, then try again.";
    }
}

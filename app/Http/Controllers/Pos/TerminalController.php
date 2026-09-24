<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\CupSize;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\SalesTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $products = Product::with('productCategory')->orderBy('product_name')->get()->each(function (Product $product) use ($imagesByKeyword) {
            $name = strtolower($product->product_name);
            $match = collect($imagesByKeyword)->first(fn ($file, $keyword) => str_contains($name, $keyword));
            $product->image = $match ?? 'placeholder.jpg';
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.cup_size_id' => ['required', 'exists:cup_sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price_at_order' => ['required', 'numeric', 'min:0'],
        ]);

        $transaction = DB::transaction(function () use ($validated, $credentialId) {
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price_at_order'];
            }

            $sale = SalesTransaction::create([
                'transaction_date' => now(),
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'credential_id' => $credentialId,
                'status' => 'completed',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::with('ingredients.inventoryItem')->findOrFail($item['product_id']);
                $cupSize = CupSize::findOrFail($item['cup_size_id']);
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
            'transaction' => $transaction->load('items.product', 'items.cupSize'),
        ], 201);
    }
}

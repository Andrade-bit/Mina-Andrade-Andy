<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\SupplyPurchase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplyPurchaseController extends Controller
{
    /**
     * List every purchase order placed with a supplier.
     */
    public function index(): View
    {
        $purchases = SupplyPurchase::with('supplier', 'items')
            ->latest('purchase_date')
            ->paginate(20);

        return view('admin.supply-purchases.index', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Show a single purchase order with its line items.
     */
    public function show(SupplyPurchase $supplyPurchase): View
    {
        $supplyPurchase->load('supplier', 'items');

        return view('admin.supply-purchases.show', [
            'purchase' => $supplyPurchase,
        ]);
    }

    /**
     * Record a new purchase order and receive its items straight into inventory.
     *
     * Expects: supplier_id, purchase_date, purchase_source, payment_method, payment_terms,
     * and items[] = [{inventory_item_id, quantity, unit_cost}, ...]
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'purchase_source' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(['Gcash', 'Cash', 'Card'])],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = SupplyPurchase::create([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'purchase_source' => $validated['purchase_source'] ?? null,
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'payment_terms' => $validated['payment_terms'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_cost'];

                $purchase->items()->attach($item['inventory_item_id'], [
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $subtotal,
                ]);

                $inventoryItem = InventoryItem::lockForUpdate()->findOrFail($item['inventory_item_id']);
                $inventoryItem->increment('current_quantity', $item['quantity']);

                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'transaction_type' => 'Restock',
                    'quantity' => $item['quantity'],
                    'inventory_transaction_date' => $validated['purchase_date'],
                    'reason' => 'Supply purchase #'.$purchase->id,
                ]);
            }
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Purchase recorded and stock received.');
    }
}

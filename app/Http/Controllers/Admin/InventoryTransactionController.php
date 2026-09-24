<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryTransactionController extends Controller
{
    /**
     * List the stock movement history, most recent first.
     */
    public function index(): View
    {
        $transactions = InventoryTransaction::with('inventoryItem')
            ->latest('inventory_transaction_date')
            ->latest('id')
            ->paginate(30);

        return view('admin.inventory-transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Record a Stock In (Restock) movement and increase the item's current quantity.
     */
    public function stockIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'inventory_transaction_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = InventoryItem::lockForUpdate()->findOrFail($validated['inventory_item_id']);
            $item->increment('current_quantity', $validated['quantity']);

            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'transaction_type' => 'Restock',
                'quantity' => $validated['quantity'],
                'inventory_transaction_date' => $validated['inventory_transaction_date'],
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Stock in recorded.');
    }

    /**
     * Record a Stock Out (Waste/Adjustment) movement and decrease the item's current quantity.
     */
    public function stockOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'transaction_type' => ['required', Rule::in(['Waste', 'Adjustment'])],
            'inventory_transaction_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = InventoryItem::lockForUpdate()->findOrFail($validated['inventory_item_id']);

            if ($validated['quantity'] > $item->current_quantity) {
                throw ValidationException::withMessages(['quantity' => 'Quantity exceeds available stock.']);
            }

            $item->decrement('current_quantity', $validated['quantity']);

            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'transaction_type' => $validated['transaction_type'],
                'quantity' => $validated['quantity'],
                'inventory_transaction_date' => $validated['inventory_transaction_date'],
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Stock out recorded.');
    }
}

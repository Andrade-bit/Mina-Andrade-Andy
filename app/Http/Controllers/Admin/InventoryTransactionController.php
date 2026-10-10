<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
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
    public function index(): RedirectResponse
    {
        return redirect()->to(route('admin.inventory').'#stock-activity');
    }

    /**
     * Record a Stock Out (Waste/Adjustment) movement and decrease the item's current quantity.
     *
     * Quantity can be entered in the item's base unit or its secondary
     * (purchase) unit — e.g. logging usage in ml even though the item is
     * bought and tracked in bottles' worth of ml. A secondary-unit entry is
     * converted to the base unit before it touches stock.
     */
    public function stockOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'quantity_unit' => ['nullable', Rule::in(['base', 'secondary'])],
            'transaction_type' => ['required', Rule::in(['Waste', 'Adjustment'])],
            'inventory_transaction_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'batch_id' => ['nullable', 'integer', 'exists:inventory_batches,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $item = InventoryItem::lockForUpdate()->findOrFail($validated['inventory_item_id']);

            $quantity = $validated['quantity'];
            if (($validated['quantity_unit'] ?? 'base') === 'secondary' && $item->conversion_factor) {
                $quantity *= (float) $item->conversion_factor;
            }

            if ($quantity > $item->current_quantity) {
                throw ValidationException::withMessages(['quantity' => 'Quantity exceeds available stock.']);
            }

            $item->consumeStock((float) $quantity, batchId: $validated['batch_id'] ?? null);

            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'transaction_type' => $validated['transaction_type'],
                'quantity' => $quantity,
                'inventory_transaction_date' => $validated['inventory_transaction_date'],
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Stock out recorded.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    /**
     * List inventory items, split into Procurement (ingredients/supplies)
     * and Supplier (cups & straws) for the Inventory screen.
     */
    public function index(): View
    {
        $procurement = InventoryItem::where('type', 'ingredient')->orderBy('name')->get();
        $supplier = InventoryItem::where('type', 'supply')->orderBy('name')->get();

        return view('admin.inventory', [
            'procurement' => $procurement,
            'supplier' => $supplier,
            'lowStockCount' => InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count(),
        ]);
    }

    /**
     * Create a new inventory item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['supply', 'ingredient'])],
            'unit' => ['required', 'string', 'max:50'],
            'current_quantity' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        InventoryItem::create($validated);

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item created.');
    }

    /**
     * Update an inventory item's details (not its stock level — see InventoryTransactionController for that).
     */
    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['supply', 'ingredient'])],
            'unit' => ['required', 'string', 'max:50'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $inventoryItem->update($validated);

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item updated.');
    }

    /**
     * Remove an inventory item.
     */
    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item removed.');
    }
}
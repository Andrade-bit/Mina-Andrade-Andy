<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    /**
     * List inventory items, split into Procurement (ingredients/supplies)
     * and Supplier (cups & straws) for the Inventory screen.
     */
    public function index(): View
    {
        $lowStockFirst = fn ($query) => $query->orderByRaw('current_quantity <= reorder_level DESC')->orderBy('name');

        $procurement = $lowStockFirst(InventoryItem::where('type', 'ingredient'))->get();
        $supplier = $lowStockFirst(InventoryItem::where('type', 'supply'))->get();

        $recentTransactions = InventoryTransaction::with('inventoryItem')
            ->latest('inventory_transaction_date')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.inventory', [
            'procurement' => $procurement,
            'supplier' => $supplier,
            'lowStockCount' => InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count(),
            'recentTransactions' => $recentTransactions,
            'suppliers' => Supplier::orderBy('supplier_name')->get(['id', 'supplier_name', 'payment_terms']),
            'stockInCount' => InventoryTransaction::where('transaction_type', 'Restock')->count(),
            'stockOutCount' => InventoryTransaction::whereIn('transaction_type', ['Waste', 'Adjustment', 'Sales'])->count(),
        ]);
    }

    /**
     * Show the form for adding a new inventory item.
     */
    public function create(): View
    {
        return view('admin.inventory-items.create');
    }

    /**
     * Create a new inventory item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['supply', 'ingredient'])],
            'image' => ['nullable', 'image', 'max:4096'],
            'unit' => ['required', 'string', 'max:50'],
            'secondary_unit' => ['nullable', 'string', 'max:50'],
            'conversion_factor' => ['nullable', 'required_with:secondary_unit', 'numeric', 'min:0.0001'],
            'current_quantity' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['image'] = $request->hasFile('image') ? $request->file('image')->store('inventory', 'public') : null;

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
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'unit' => ['required', 'string', 'max:50'],
            'secondary_unit' => ['nullable', 'string', 'max:50'],
            'conversion_factor' => ['nullable', 'required_with:secondary_unit', 'numeric', 'min:0.0001'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $imagePath = $inventoryItem->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('inventory', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $validated['image'] = $imagePath;
        unset($validated['remove_image']);

        $inventoryItem->update($validated);

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item updated.');
    }

    /**
     * Remove an inventory item.
     */
    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item archived.');
    }
}

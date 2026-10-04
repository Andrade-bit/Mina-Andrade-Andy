<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CupSize;
use App\Models\InventoryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CupSizeController extends Controller
{
    /**
     * List every cup size and its price (Small ₱35 / Medium ₱40 / Large ₱50).
     */
    public function index(Request $request): View
    {
        $cupSizes = CupSize::query()->when($request->boolean('archived'), fn ($q) => $q->onlyTrashed())->withCount('salesTransactionItems')->with('inventoryItem')->orderBy('price')->get();

        return view('admin.cup-sizes.index', [
            'cupSizes' => $cupSizes,
            'inventoryItems' => InventoryItem::orderBy('name')->get(),
        ]);
    }

    /**
     * Create a new cup size, optionally linked to its cup stock item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'size_name' => ['required', 'string', 'max:255'],
            'inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        CupSize::create($validated);

        return redirect()->route('admin.cup-sizes.index')->with('status', 'Cup size created.');
    }

    /**
     * Update a cup size.
     */
    public function update(Request $request, CupSize $cupSize): RedirectResponse
    {
        $validated = $request->validate([
            'size_name' => ['required', 'string', 'max:255'],
            'inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $cupSize->update($validated);

        return redirect()->route('admin.cup-sizes.index')->with('status', 'Cup size updated.');
    }

    /**
     * Delete a cup size.
     */
    public function destroy(CupSize $cupSize): RedirectResponse
    {
        $cupSize->delete();

        return redirect()->route('admin.cup-sizes.index')->with('status', 'Cup size archived.');
    }

    /**
     * Bring an archived cup size back.
     */
    public function restore(int $id): RedirectResponse
    {
        CupSize::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.cup-sizes.index', ['archived' => 1])->with('status', 'Cup size restored.');
    }
}

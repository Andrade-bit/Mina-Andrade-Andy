<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CupSize;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CupSizeController extends Controller
{
    /**
     * List every cup size and its price (Small ₱35 / Medium ₱40 / Large ₱50).
     */
    public function index(): View
    {
        $cupSizes = CupSize::with('inventoryItem')->orderBy('price')->get();

        return view('admin.cup-sizes.index', [
            'cupSizes' => $cupSizes,
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

        return redirect()->route('admin.cup-sizes.index')->with('status', 'Cup size removed.');
    }
}
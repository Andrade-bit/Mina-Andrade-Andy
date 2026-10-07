<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CupSize;
use App\Models\InventoryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
     * Fields shared by create and update. Only one size can be the one recipes are written for, and that
     * size needs a volume so the others can be scaled from it.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'size_name' => ['required', 'string', 'max:255'],
            'inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'volume_ml' => [Rule::requiredIf($request->boolean('is_recipe_size')), 'nullable', 'numeric', 'min:1', 'max:5000'],
            'is_recipe_size' => ['nullable', 'boolean'],
        ], [
            'volume_ml.required' => 'Enter the volume in ml: this is the size recipes are written for.',
        ]);

        $validated['is_recipe_size'] = $request->boolean('is_recipe_size');

        return $validated;
    }

    /**
     * Create a new cup size, optionally linked to its cup stock item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($validated) {
            if ($validated['is_recipe_size']) {
                CupSize::query()->update(['is_recipe_size' => false]);
            }

            CupSize::create($validated);
        });

        return redirect()->route('admin.cup-sizes.index')->with('status', 'Cup size created.');
    }

    /**
     * Update a cup size.
     */
    public function update(Request $request, CupSize $cupSize): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($validated, $cupSize) {
            if ($validated['is_recipe_size']) {
                CupSize::whereKeyNot($cupSize->id)->update(['is_recipe_size' => false]);
            }

            $cupSize->update($validated);
        });

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

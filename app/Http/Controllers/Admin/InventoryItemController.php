<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    /**
     * List what is already in stock, split into Ingredients and Cups & Straws. Buying more happens in
     * Supply Purchases. "?stock=low" narrows the list to items at or below their reorder level.
     */
    public function index(Request $request): View
    {
        $onlyLow = $request->query('stock') === 'low';

        $lowStockFirst = fn ($query) => $query
            ->when($onlyLow, fn ($q) => $q->whereColumn('current_quantity', '<=', 'reorder_level'))
            ->orderByRaw('current_quantity <= reorder_level DESC')
            ->orderBy('name');

        $withUsage = fn ($query) => $query->withCount('cupSizes')->with('ingredients.products:id');

        $procurement = $lowStockFirst($withUsage(InventoryItem::where('type', 'ingredient')))->get();
        $supplier = $lowStockFirst($withUsage(InventoryItem::where('type', 'supply')))->get();
        $archived = InventoryItem::onlyTrashed()->orderBy('name')->get();

        $recentTransactions = InventoryTransaction::with('inventoryItem')
            ->latest('inventory_transaction_date')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.inventory', [
            'procurement' => $procurement,
            'supplier' => $supplier,
            'archived' => $archived,
            'lowStockCount' => InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count(),
            'recentTransactions' => $recentTransactions,
            'onlyLow' => $onlyLow,
            'totalItems' => InventoryItem::count(),
            'itemOptions' => InventoryItem::orderBy('name')->get()->map->toFormOption()->values(),
            'stockInCount' => InventoryTransaction::where('transaction_type', 'Restock')->count(),
            'stockOutCount' => InventoryTransaction::whereIn('transaction_type', ['Waste', 'Adjustment', 'Sales'])->count(),
        ]);
    }

    /**
     * Show the form for editing an inventory item's details and units.
     */
    public function edit(InventoryItem $inventoryItem): View
    {
        return view('admin.inventory-items.edit', ['item' => $inventoryItem]);
    }

    /**
     * Update an inventory item's details (not its stock level — see InventoryTransactionController for that).
     * Changing the stock unit converts everything recorded in the old one (stock, history, purchases, recipes).
     */
    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $unitChanged = $request->input('unit') !== $inventoryItem->unit;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['supply', 'ingredient'])],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            ...$this->unitRules($request),
            'unit_conversion' => [Rule::requiredIf($unitChanged), 'nullable', 'numeric', 'gt:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
        ], $this->unitMessages() + [
            'unit_conversion.required' => "Tell us how many {$request->input('unit')} one {$inventoryItem->unit} is, so the current stock can be converted.",
        ]);

        $validated = $this->resolvePurchaseUnit($validated);
        $unitConversion = $unitChanged ? (float) $validated['unit_conversion'] : null;
        unset($validated['unit_conversion']);

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

        DB::transaction(function () use ($inventoryItem, $validated, $unitConversion) {
            if ($unitConversion !== null) {
                $inventoryItem->convertStockUnit($unitConversion);
            }

            $inventoryItem->update($validated);
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item updated.');
    }

    /**
     * Validation for the fixed stock unit and the unit the item is bought in.
     *
     * @return array<string, array<int, mixed>>
     */
    private function unitRules(Request $request): array
    {
        $baseUnit = (string) $request->input('unit');
        $purchaseUnit = (string) $request->input('secondary_unit');
        $isKnownUnit = array_key_exists($purchaseUnit, InventoryItem::PURCHASE_UNITS[$baseUnit] ?? []);
        $needsSize = $isKnownUnit && InventoryItem::fixedPurchaseFactor($baseUnit, $purchaseUnit) === null;

        return [
            'unit' => ['required', Rule::in(array_keys(InventoryItem::BASE_UNITS))],
            'secondary_unit' => ['nullable', Rule::in(array_keys(InventoryItem::PURCHASE_UNITS[$baseUnit] ?? []))],
            'conversion_factor' => [$needsSize ? 'required' : 'nullable', 'numeric', 'min:0.0001'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function unitMessages(): array
    {
        return [
            'unit.in' => 'Stock unit must be ml, g or pcs.',
            'secondary_unit.in' => 'That purchase unit does not match the stock unit. Pick one from the list.',
            'conversion_factor.required' => 'Enter how much one purchase unit holds (for example, 1 bottle = 750 ml).',
        ];
    }

    /**
     * Fixed-size purchase units (L, kg, dozen...) get their conversion automatically; with no purchase
     * unit there is nothing to convert.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function resolvePurchaseUnit(array $validated): array
    {
        if (empty($validated['secondary_unit'])) {
            $validated['secondary_unit'] = null;
            $validated['conversion_factor'] = null;

            return $validated;
        }

        $fixed = InventoryItem::fixedPurchaseFactor($validated['unit'], $validated['secondary_unit']);

        if ($fixed !== null) {
            $validated['conversion_factor'] = $fixed;
        }

        return $validated;
    }

    /**
     * Archive an inventory item (mark it as unused). Its history is kept and it can be restored.
     */
    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item archived.');
    }

    /**
     * Bring an archived inventory item back into use.
     */
    public function restore(int $id): RedirectResponse
    {
        InventoryItem::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.inventory-items.index')->with('status', 'Inventory item restored.');
    }
}

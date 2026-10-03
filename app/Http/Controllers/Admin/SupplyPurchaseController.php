<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
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
        $supplyPurchase->load('supplier', 'items', 'expense');

        return view('admin.supply-purchases.show', [
            'purchase' => $supplyPurchase,
        ]);
    }

    /**
     * Record a new purchase invoice and receive its items straight into inventory.
     *
     * Expects: supplier_id, purchase_date, invoice_number, purchase_source, payment_method,
     * payment_terms, tax_rate, notes, and items[] = [{inventory_item_id, quantity, unit_cost, quantity_unit}, ...]
     *
     * A line's quantity_unit is "base" (the item's tracked unit) or "secondary" (its
     * purchase unit, e.g. "bottle") — secondary-unit quantities are converted to the
     * base unit using the item's conversion_factor before stock and the ledger are touched.
     * This also auto-creates a matching Expense so Stock In always shows up in Expenses.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'purchase_source' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(['Gcash', 'Cash', 'Card'])],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.quantity_unit' => ['nullable', Rule::in(['base', 'secondary'])],
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $itemsSubtotal = 0;

            foreach ($validated['items'] as $item) {
                $itemsSubtotal += $item['quantity'] * $item['unit_cost'];
            }

            $taxAmount = round($itemsSubtotal * ((float) ($validated['tax_rate'] ?? 0) / 100), 2);

            $purchase = SupplyPurchase::create([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'purchase_source' => $validated['purchase_source'] ?? null,
                'payment_method' => $validated['payment_method'],
                'total_amount' => $itemsSubtotal + $taxAmount,
                'tax_amount' => $taxAmount,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $inventoryItem = InventoryItem::lockForUpdate()->findOrFail($item['inventory_item_id']);
                $lineTotal = $item['quantity'] * $item['unit_cost'];

                $baseQuantity = $item['quantity'];
                if (($item['quantity_unit'] ?? 'base') === 'secondary' && $inventoryItem->conversion_factor) {
                    $baseQuantity = $item['quantity'] * (float) $inventoryItem->conversion_factor;
                }

                $baseUnitCost = $baseQuantity > 0 ? $lineTotal / $baseQuantity : 0;

                $purchase->items()->attach($inventoryItem->id, [
                    'quantity' => $baseQuantity,
                    'unit_cost' => $baseUnitCost,
                    'subtotal' => $lineTotal,
                ]);

                $inventoryItem->increment('current_quantity', $baseQuantity);

                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'transaction_type' => 'Restock',
                    'quantity' => $baseQuantity,
                    'inventory_transaction_date' => $validated['purchase_date'],
                    'reason' => 'Supply purchase #'.$purchase->id,
                ]);
            }

            $inventoryCategory = ExpenseCategory::firstOrCreate(['category_name' => 'Inventory Purchases']);

            Expense::create([
                'expense_category_id' => $inventoryCategory->id,
                'supply_purchase_id' => $purchase->id,
                'description' => 'Purchase #'.$purchase->id.($validated['invoice_number'] ?? '' ? ' ('.$validated['invoice_number'].')' : ''),
                'amount' => $purchase->total_amount,
                'expense_date' => $validated['purchase_date'],
                'payment_method' => $validated['payment_method'],
            ]);

            return $purchase;
        });

        return redirect()->route('admin.inventory-items.index')->with('status', 'Purchase #'.$purchase->id.' recorded, stock received, and logged in Expenses.');
    }
}

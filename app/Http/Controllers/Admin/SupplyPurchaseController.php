<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use App\Models\SupplyPurchase;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SupplyPurchaseController extends Controller
{
    /**
     * List every purchase and host the "Record Purchase" form: this is where stock is bought, and each
     * purchase adds to Inventory. "?payment_method=" narrows the list, "?restock={item id}" opens the form
     * with that item already picked.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'highest', 'lowest'])],
        ]);
        $search = trim($validated['search'] ?? '');
        $sort = $validated['sort'] ?? 'newest';
        [$column, $direction] = match ($sort) {
            'oldest' => ['purchase_date', 'asc'],
            'highest' => ['total_amount', 'desc'],
            'lowest' => ['total_amount', 'asc'],
            default => ['purchase_date', 'desc'],
        };
        $purchases = SupplyPurchase::with('supplier', 'items')
            ->when($request->filled('payment_method'), fn ($query) => $query->where('payment_method', $request->string('payment_method')))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $pattern = '%'.$search.'%';
                    $q->where('invoice_number', 'like', $pattern)
                        ->orWhere('purchase_source', 'like', $pattern)
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('supplier_name', 'like', $pattern))
                        ->orWhereHas('items', fn ($items) => $items->where('inventory_items.name', 'like', $pattern));
                    $reference = ltrim($search, '#');
                    if (ctype_digit($reference)) {
                        $q->orWhere('supply_purchases.id', $reference);
                    }
                });
            })
            ->orderBy($column, $direction)->orderBy('id', $direction)
            ->paginate(20)
            ->withQueryString();

        return view('admin.supply-purchases.index', [
            'purchases' => $purchases,
            'search' => $search,
            'sort' => $sort,
            'suppliers' => Supplier::orderBy('supplier_name')->get(['id', 'supplier_name', 'payment_terms']),
            'itemOptions' => InventoryItem::orderBy('name')->get()->map->toFormOption()->values(),
            'restockItemId' => $request->integer('restock') ?: null,
            'openForm' => $request->boolean('record'),
        ]);
    }

    /**
     * Validation for lines that bring in a brand-new item ("New item" on the purchase form): its name, kind,
     * the fixed stock unit (ml, g or pcs) and, if it is bought by the bottle, can or box, how much one holds.
     *
     * @return array<string, array<int, mixed>>
     */
    private function newItemRules(Request $request): array
    {
        $rules = [];
        $namesInThisPurchase = [];

        foreach ((array) $request->input('items', []) as $index => $line) {
            if (($line['inventory_item_id'] ?? null) !== 'new') {
                continue;
            }

            $unit = (string) ($line['new_item']['unit'] ?? '');
            $buy = (string) ($line['new_item']['secondary_unit'] ?? '');
            $isKnownUnit = array_key_exists($buy, InventoryItem::PURCHASE_UNITS[$unit] ?? []);
            $needsSize = $isKnownUnit && InventoryItem::fixedPurchaseFactor($unit, $buy) === null;

            $rules["items.{$index}.new_item.name"] = ['required', 'string', 'max:255', function (string $attribute, mixed $value, Closure $fail) use (&$namesInThisPurchase) {
                $key = mb_strtolower(trim($value));
                $existing = InventoryItem::withTrashed()->whereRaw('LOWER(TRIM(name)) = ?', [$key])->first();

                if ($existing) {
                    $fail($existing->trashed()
                        ? "\"{$existing->name}\" is archived. Restore it from Inventory > Archived instead of adding it again."
                        : "\"{$existing->name}\" already exists. Pick it from the item list instead.");
                } elseif (in_array($key, $namesInThisPurchase, true)) {
                    $fail("\"{$value}\" is on this purchase twice. Combine them into one line.");
                }

                $namesInThisPurchase[] = $key;
            }];
            $rules["items.{$index}.new_item.type"] = ['required', Rule::in(['ingredient', 'supply'])];
            $rules["items.{$index}.new_item.unit"] = ['required', Rule::in(array_keys(InventoryItem::BASE_UNITS))];
            $rules["items.{$index}.new_item.secondary_unit"] = ['nullable', Rule::in(array_keys(InventoryItem::PURCHASE_UNITS[$unit] ?? []))];
            $rules["items.{$index}.new_item.conversion_factor"] = [$needsSize ? 'required' : 'nullable', 'numeric', 'min:0.0001'];
            $rules["items.{$index}.new_item.reorder_level"] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /**
     * Create the item behind a "New item" line. It starts with no stock; the purchase line brings it in.
     *
     * @param  array<string, mixed>  $new
     */
    private function createItem(array $new): InventoryItem
    {
        $buy = $new['secondary_unit'] ?? null;
        $factor = $buy ? (InventoryItem::fixedPurchaseFactor($new['unit'], $buy) ?? (float) $new['conversion_factor']) : null;

        return InventoryItem::create([
            'name' => trim($new['name']),
            'type' => $new['type'],
            'unit' => $new['unit'],
            'secondary_unit' => $buy ?: null,
            'conversion_factor' => $factor,
            'current_quantity' => 0,
            'reorder_level' => $new['reorder_level'] ?? 0,
            'status' => 'active',
        ]);
    }

    /**
     * Show a single purchase order with its line items.
     */
    public function show(SupplyPurchase $supplyPurchase): View
    {
        $supplyPurchase->load('supplier', 'items', 'expense', 'batches');

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
        if (is_string($request->input('invoice_number'))) {
            $request->merge(['invoice_number' => mb_strtoupper(trim($request->input('invoice_number'))) ?: null]);
        }
        $validated = $request->validate([
            ...$this->newItemRules($request),
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:255', Rule::unique('supply_purchases', 'invoice_number')],
            'purchase_source' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in(['Gcash', 'Cash', 'Card'])],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', fn (string $attribute, mixed $value, Closure $fail) => $value === 'new' || InventoryItem::whereKey($value)->exists() || $fail('Pick an item for every line.')],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.quantity_unit' => ['nullable', Rule::in(['base', 'secondary'])],
            'items.*.unit_size' => ['nullable', 'numeric', 'min:0.0001'],
            'items.*.has_expiry' => ['nullable', 'boolean'],
            'items.*.expires_at' => ['exclude_unless:items.*.has_expiry,1', 'required', 'date_format:Y-m-d', 'after_or_equal:purchase_date'],
        ], [
            'items.*.inventory_item_id.required' => 'Pick an item (or New item) for every line.',
            'items.*.quantity.required' => 'Enter a quantity for every line.',
            'items.*.unit_cost.required' => 'Enter the cost for every line.',
            'items.*.expires_at.required' => 'Enter the expiry date or turn off Has expiry date.',
            'items.*.expires_at.date_format' => 'Enter a valid expiry date.',
            'items.*.expires_at.after_or_equal' => 'Expiry date cannot be before the purchase date.',
            'items.*.new_item.name.required' => 'Type a name for the new item.',
            'items.*.new_item.type.required' => 'Choose whether the new item is an ingredient or cups and straws.',
            'items.*.new_item.unit.required' => 'Choose a stock unit (ml, g or pcs) for the new item.',
            'items.*.new_item.unit.in' => 'The stock unit must be ml, g or pcs.',
            'items.*.new_item.secondary_unit.in' => 'That "Bought as" unit does not fit the stock unit. Pick one from the list.',
            'items.*.new_item.conversion_factor.required' => 'Enter how much one bottle, can or box of the new item holds.',
            'items.*.new_item.conversion_factor.min' => 'How much one holds must be more than 0.',
        ]);

        try {
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
                    $inventoryItem = $item['inventory_item_id'] === 'new'
                        ? $this->createItem($item['new_item'])
                        : InventoryItem::lockForUpdate()->findOrFail($item['inventory_item_id']);
                    $lineTotal = $item['quantity'] * $item['unit_cost'];

                    // Buying by the bottle/can/box: use this purchase's own pack size when given (brands differ),
                    // otherwise the size saved on the item. Fixed units like L and kg never change.
                    $baseQuantity = $item['quantity'];
                    if (($item['quantity_unit'] ?? 'base') === 'secondary') {
                        $isFixedSize = $inventoryItem->secondary_unit
                            && InventoryItem::fixedPurchaseFactor($inventoryItem->unit, $inventoryItem->secondary_unit) !== null;
                        $unitSize = $isFixedSize ? 0 : (float) ($item['unit_size'] ?? 0);
                        $factor = $unitSize > 0 ? $unitSize : (float) $inventoryItem->conversion_factor;

                        if ($factor > 0) {
                            $baseQuantity = $item['quantity'] * $factor;
                        }
                    }

                    $previous = $purchase->items()->where('inventory_items.id', $inventoryItem->id)->first()?->pivot;
                    $combinedQuantity = $baseQuantity + (float) ($previous?->quantity ?? 0);
                    $combinedSubtotal = $lineTotal + (float) ($previous?->subtotal ?? 0);
                    $purchase->items()->syncWithoutDetaching([$inventoryItem->id => [
                        'quantity' => $combinedQuantity,
                        'unit_cost' => $combinedSubtotal / $combinedQuantity,
                        'subtotal' => $combinedSubtotal,
                    ]]);

                    $inventoryItem->batches()->create([
                        'supply_purchase_id' => $purchase->id,
                        'quantity' => $baseQuantity,
                        'remaining_quantity' => $baseQuantity,
                        'expires_at' => ! empty($item['has_expiry']) ? ($item['expires_at'] ?? null) : null,
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
        } catch (UniqueConstraintViolationException $exception) {
            if (isset($validated['invoice_number']) && SupplyPurchase::where('invoice_number', $validated['invoice_number'])->exists()) {
                throw ValidationException::withMessages(['invoice_number' => 'This invoice/reference number has already been recorded.']);
            }
            throw $exception;
        }

        return redirect()->route('admin.supply-purchases.index')->with('status', 'Purchase #'.$purchase->id.' recorded, stock received, and logged in Expenses.');
    }
}

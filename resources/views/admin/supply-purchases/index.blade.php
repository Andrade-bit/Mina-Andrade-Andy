<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Supply Purchases</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          display: ['"Baloo 2"', 'sans-serif'],
          body: ['"Nunito"', 'sans-serif'],
        },
        colors: {
          cream: { 50: '#FFFDF9', 100: '#FBF3E4', 200: '#F1E6CC' },
          stamp: { 50: '#EAF3FA', 100: '#D3E6F3', 300: '#8FBBDD', 500: '#2F6690', 600: '#24506F', 700: '#1A3B52' },
          mint: { 50: '#EAF5EF', 500: '#6FAE8B', 600: '#3F7457' },
          coral: { 50: '#FBEDEB', 500: '#E0776B', 600: '#9C4A41' },
        },
        boxShadow: {
          soft: '0 16px 32px -10px rgba(26,59,82,0.16), 0 4px 10px rgba(26,59,82,0.06)',
          'soft-sm': '0 6px 14px -4px rgba(26,59,82,0.15)',
          'soft-inset': 'inset 0 2px 6px rgba(26,59,82,0.14), inset 0 -1px 1px rgba(255,255,255,0.7)',
          'soft-btn': '0 5px 0 #1A3B52, 0 10px 18px rgba(47,102,144,0.3)',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex">

  @include('admin.partials.sidebar', ['active' => 'supply-purchases'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Supply Purchases</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Buy stock here. Every purchase adds to Inventory and is logged in Expenses</p>
      </div>
      <button type="button" onclick="openStockIn()" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Record Purchase
      </button>
    </div>


    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-4">Ref #</th>
            <th class="py-2 px-4">Date</th>
            <th class="py-2 px-4">Bought From</th>
            <th class="py-2 px-4">Items</th>
            <th class="py-2 px-4">Payment</th>
            <th class="py-2 px-4">Total</th>
            <th class="py-2 px-4"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($purchases as $purchase)
            <tr class="border-b border-cream-200 last:border-0">
              <td class="py-3 px-4 font-bold text-stamp-700 text-sm whitespace-nowrap">{{ $purchase->invoice_number ?: '#'.$purchase->id }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm whitespace-nowrap">{{ $purchase->purchase_date->format('M j, Y') }}</td>
              <td class="py-3 px-4 font-bold text-stamp-700 text-sm">{{ $purchase->supplier->supplier_name ?? $purchase->purchase_source ?? 'Unspecified' }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $purchase->items->count() }} {{ Str::plural('item', $purchase->items->count()) }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $purchase->payment_method }}</td>
              <td class="py-3 px-4 font-display font-bold text-stamp-700">₱{{ number_format($purchase->total_amount, 2) }}</td>
              <td class="py-3 px-4">
                <a href="{{ route('admin.supply-purchases.show', $purchase) }}" class="text-stamp-500 text-xs font-bold hover:underline">View &rarr;</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="py-10 text-center text-sm font-semibold text-stamp-300">No purchases recorded yet. Click "Record Purchase" to log one.</td></tr>
          @endforelse
        </tbody>
      </table>

      @include('admin.partials.pagination', ['paginator' => $purchases])
    </div>
  </main>

  <!-- Record Purchase Modal (multi-line invoice) -->
  <div id="stockInModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-2xl p-7 relative max-h-[92vh] overflow-y-auto">
      <button type="button" onclick="closeModal('stockInModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-mint-50 shadow-soft-inset mx-auto flex items-center justify-center text-mint-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Record a Purchase</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">Log a purchase receipt and receive the stock into inventory in one go</p>
      </div>
      <form method="POST" action="{{ route('admin.supply-purchases.store') }}" id="stockInForm" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Invoice / Ref #</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="e.g. INV-00231" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Date</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Supplier <span class="text-stamp-300 normal-case font-semibold">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="supplier_id" onchange="onSupplierChange(this)" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="" data-terms="">No supplier (bought at a store)</option>
              @foreach ($suppliers as $vendor)
                <option value="{{ $vendor->id }}" data-terms="{{ $vendor->payment_terms }}" @selected((int) old('supplier_id') === $vendor->id)>{{ $vendor->supplier_name }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" name="payment_terms" id="stockInPaymentTerms" value="{{ old('payment_terms') }}">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Bought From</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="text" name="purchase_source" value="{{ old('purchase_source') }}" placeholder="e.g. NCCC Mall" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Paid Via</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <select name="payment_method" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                <option value="Cash" @selected(old('payment_method', 'Cash') === 'Cash')>Cash</option>
                <option value="Gcash" @selected(old('payment_method') === 'Gcash')>GCash</option>
                <option value="Card" @selected(old('payment_method') === 'Card')>Card</option>
              </select>
            </div>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5 ml-1">
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500">Line Items</label>
            <button type="button" onclick="addStockInRow()" class="text-[11px] font-extrabold text-stamp-500 hover:text-stamp-600 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Add Line
            </button>
          </div>
          <div id="stockInRows" class="space-y-2"></div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Notes (optional)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="e.g. Short delivery, 2 boxes missing" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-soft-inset p-4 space-y-2">
          <div class="flex items-center justify-between text-sm">
            <span class="text-stamp-400 font-semibold">Subtotal</span>
            <span id="stockInSubtotal" class="font-display font-bold text-stamp-700">₱0.00</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-stamp-400 font-semibold flex items-center gap-2">
              Tax
              <input type="number" min="0" max="100" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}" oninput="recalcStockInTotals()" class="w-16 bg-cream-100 rounded-lg shadow-soft-inset px-2 py-1 text-xs font-bold text-stamp-700 outline-none">
              <span class="text-xs">%</span>
            </span>
            <span id="stockInTax" class="font-display font-bold text-stamp-700">₱0.00</span>
          </div>
          <div class="flex items-center justify-between text-base pt-2 border-t border-cream-200">
            <span class="text-stamp-700 font-extrabold">Total</span>
            <span id="stockInTotal" class="font-display font-bold text-lg text-mint-600">₱0.00</span>
          </div>
        </div>

        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-mint-500 to-mint-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn-mint active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Confirm Purchase &amp; Log Expense
        </button>
      </form>
    </div>
  </div>


  <script>
    const INVENTORY_ITEMS = @json($itemOptions);

    let stockInRowCount = 0;

    function onSupplierChange(select) {
      const option = select.options[select.selectedIndex];
      document.getElementById('stockInPaymentTerms').value = option.dataset.terms || '';
      const sourceInput = document.querySelector('#stockInForm input[name="purchase_source"]');
      if (sourceInput && select.value && !sourceInput.value) {
        sourceInput.value = option.textContent.trim();
      }
    }

    function stockInRowTemplate(index) {
      const options = INVENTORY_ITEMS.map(i => `<option value="${i.id}">${i.name}</option>`).join('');
      return `
        <div class="stock-in-row bg-cream-100 rounded-2xl shadow-soft-inset p-3 grid grid-cols-12 gap-2 items-center" data-index="${index}">
          <select name="items[${index}][inventory_item_id]" onchange="onStockInItemChange(this)" class="col-span-4 bg-cream-50 rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
            <option value="">Select item…</option>
            <option value="new">＋ New item…</option>
            ${options}
          </select>
          <input type="number" min="0.01" step="0.01" name="items[${index}][quantity]" placeholder="Qty" oninput="recalcStockInTotals(); updateStockInConversion(this.closest('.stock-in-row'))" class="col-span-2 bg-cream-50 rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
          <select name="items[${index}][quantity_unit]" onchange="updateStockInConversion(this.closest('.stock-in-row'))" class="stock-in-unit col-span-2 bg-cream-50 rounded-xl px-1 py-2 text-[11px] font-bold text-stamp-700 outline-none">
            <option value="base">base</option>
          </select>
          <input type="number" min="0" step="0.01" name="items[${index}][unit_cost]" placeholder="Cost" oninput="recalcStockInTotals()" class="col-span-2 bg-cream-50 rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
          <span class="stock-in-amount col-span-1 text-xs font-display font-bold text-stamp-700 text-right">₱0</span>
          <button type="button" onclick="removeStockInRow(this)" class="col-span-1 text-coral-500 hover:text-coral-600 flex justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
          <div class="stock-in-new hidden col-span-12 grid grid-cols-2 sm:grid-cols-6 gap-2 bg-cream-50 rounded-xl p-3">
            <p class="col-span-2 sm:col-span-6 text-[11px] font-extrabold uppercase tracking-wide text-stamp-500">New item details <span class="normal-case font-semibold text-stamp-300">(it starts at 0 and this line brings in its first stock)</span></p>
            <input type="text" name="items[${index}][new_item][name]" placeholder="Item name, e.g. Vanilla Syrup" aria-label="New item name" disabled class="new-field col-span-2 sm:col-span-3 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
            <select name="items[${index}][new_item][type]" aria-label="New item kind" disabled class="new-field col-span-2 sm:col-span-3 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
              <option value="ingredient">Ingredient</option>
              <option value="supply">Cups &amp; straws</option>
            </select>
            <select name="items[${index}][new_item][unit]" aria-label="Stock unit" onchange="onNewItemChange(this)" disabled class="new-field col-span-1 sm:col-span-2 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
              <option value="">Stock unit…</option>
              <option value="ml">ml</option>
              <option value="g">g</option>
              <option value="pcs">pcs</option>
            </select>
            <select name="items[${index}][new_item][secondary_unit]" aria-label="Bought as" onchange="onNewItemChange(this)" disabled class="new-field col-span-1 sm:col-span-2 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
              <option value="">Bought as… (optional)</option>
            </select>
            <input type="number" min="0.0001" step="0.01" name="items[${index}][new_item][conversion_factor]" placeholder="Size" aria-label="How much one holds" oninput="onNewItemChange(this)" disabled class="new-field new-size hidden col-span-1 sm:col-span-1 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
            <input type="number" min="0" step="0.01" name="items[${index}][new_item][reorder_level]" placeholder="Reorder at" aria-label="Reorder level" disabled class="new-field col-span-1 sm:col-span-1 bg-white rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
          </div>
          <div class="stock-in-size hidden col-span-12 flex items-center gap-2 pl-1">
            <span class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-400 shrink-0">Pack size <span class="normal-case font-semibold text-stamp-300">(optional)</span></span>
            <input type="number" min="0.0001" step="0.01" name="items[${index}][unit_size]" oninput="updateStockInConversion(this.closest('.stock-in-row'))" class="stock-in-size-input w-24 bg-cream-50 rounded-xl px-2 py-1.5 text-xs font-bold text-stamp-700 outline-none">
            <span class="stock-in-size-note text-[11px] font-bold text-stamp-400"></span>
          </div>
          <p class="stock-in-conversion hidden col-span-12 text-[11px] font-bold text-mint-600 pl-1"></p>
        </div>`;
    }

    // For bottles, cans and boxes: lets this purchase say how big each one is (brands differ) and shows
    // what the line adds to stock, e.g. "2 bottle = 1,500 ml added to stock".
    function updateStockInConversion(row) {
      const note = row.querySelector('.stock-in-conversion');
      const sizeBox = row.querySelector('.stock-in-size');
      const sizeInput = row.querySelector('.stock-in-size-input');
      const item = rowItem(row);
      const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value);
      const usingPurchaseUnit = row.querySelector('.stock-in-unit').value === 'secondary';
      const savedSize = item && item.conversion_factor ? parseFloat(item.conversion_factor) : 0;

      const asksForSize = !!item && item.id !== 'new' && usingPurchaseUnit && savedSize > 0 && !item.fixed_size;
      sizeBox.classList.toggle('hidden', !asksForSize);
      if (asksForSize) {
        sizeInput.placeholder = savedSize;
        row.querySelector('.stock-in-size-note').textContent = item.unit + ' per ' + item.secondary_unit + '. Blank uses the saved ' + savedSize.toLocaleString() + ' ' + item.unit + '.';
      }

      if (!item || !usingPurchaseUnit || !(qty > 0) || !(savedSize > 0)) {
        note.classList.add('hidden');
        return;
      }
      const typedSize = asksForSize ? parseFloat(sizeInput.value) : 0;
      const size = typedSize > 0 ? typedSize : savedSize;
      note.textContent = qty + ' ' + item.secondary_unit + ' = ' + (qty * size).toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' ' + item.unit + ' added to stock';
      note.classList.remove('hidden');
    }

    const PURCHASE_UNITS = @json(App\Models\InventoryItem::PURCHASE_UNITS);

    // The item a line is for: an existing one, or the one being described in its "New item" fields.
    function rowItem(row) {
      const value = row.querySelector('select[name*="[inventory_item_id]"]').value;
      if (value !== 'new') { return INVENTORY_ITEMS.find(i => String(i.id) === value); }

      const field = key => row.querySelector('[name$="[new_item][' + key + ']"]');
      const unit = field('unit').value;
      const buy = field('secondary_unit').value;
      const fixed = unit && buy ? PURCHASE_UNITS[unit][buy] : null;
      const size = buy ? (fixed != null ? Number(fixed) : (parseFloat(field('conversion_factor').value) || 0)) : 0;

      return { id: 'new', name: field('name').value, unit: unit || 'base', secondary_unit: buy || null, conversion_factor: size || null, fixed_size: fixed != null };
    }

    // Rebuilds the line's unit choices (base, and the purchase unit when there is one).
    function refreshUnitSelect(row, keepChoice) {
      const unitSelect = row.querySelector('.stock-in-unit');
      const keep = unitSelect.value;
      const item = rowItem(row);
      unitSelect.innerHTML = '<option value="base">' + (item ? item.unit : 'base') + '</option>';
      if (item && item.secondary_unit && item.conversion_factor) {
        unitSelect.insertAdjacentHTML('beforeend', `<option value="secondary">${item.secondary_unit}</option>`);
      }
      if (keepChoice && Array.prototype.some.call(unitSelect.options, o => o.value === keep)) { unitSelect.value = keep; }
      updateStockInConversion(row);
    }

    // A "New item" field changed: offer the purchase units that fit the stock unit, show the size box when it varies.
    function onNewItemChange(el) {
      const row = el.closest('.stock-in-row');
      const unit = row.querySelector('[name$="[new_item][unit]"]').value;
      const buySelect = row.querySelector('[name$="[new_item][secondary_unit]"]');

      if (el.name.endsWith('[unit]')) {
        buySelect.innerHTML = '<option value="">Bought as… (optional)</option>' + Object.keys(PURCHASE_UNITS[unit] || {}).map(u => {
          const fixed = PURCHASE_UNITS[unit][u];
          return '<option value="' + u + '">' + u + (fixed ? ' (= ' + Number(fixed).toLocaleString() + ' ' + unit + ')' : '') + '</option>';
        }).join('');
      }

      const buy = buySelect.value;
      const varies = !!unit && !!buy && PURCHASE_UNITS[unit][buy] == null;
      const sizeInput = row.querySelector('.new-size');
      sizeInput.classList.toggle('hidden', !varies);
      sizeInput.disabled = !varies;
      sizeInput.placeholder = buy ? '1 ' + buy + ' = ? ' + unit : 'Size';

      refreshUnitSelect(row, true);
    }

    function addStockInRow() {
      const container = document.getElementById('stockInRows');
      container.insertAdjacentHTML('beforeend', stockInRowTemplate(stockInRowCount));
      stockInRowCount++;
    }

    function removeStockInRow(btn) {
      const rows = document.querySelectorAll('.stock-in-row');
      if (rows.length <= 1) { return; }
      btn.closest('.stock-in-row').remove();
      recalcStockInTotals();
    }

    function onStockInItemChange(select) {
      const row = select.closest('.stock-in-row');
      const isNew = select.value === 'new';
      row.querySelector('.stock-in-new').classList.toggle('hidden', !isNew);
      row.querySelectorAll('.new-field').forEach(el => { el.disabled = !isNew; });
      refreshUnitSelect(row, false);
    }

    function recalcStockInTotals() {
      let subtotal = 0;
      document.querySelectorAll('.stock-in-row').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const cost = parseFloat(row.querySelector('input[name*="[unit_cost]"]').value) || 0;
        const amount = qty * cost;
        row.querySelector('.stock-in-amount').textContent = '₱' + amount.toFixed(0);
        subtotal += amount;
      });
      const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
      const tax = subtotal * (taxRate / 100);
      const total = subtotal + tax;
      document.getElementById('stockInSubtotal').textContent = '₱' + subtotal.toFixed(2);
      document.getElementById('stockInTax').textContent = '₱' + tax.toFixed(2);
      document.getElementById('stockInTotal').textContent = '₱' + total.toFixed(2);
    }

    function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

    // Opens the purchase form; pass an inventory item id to start with that item already picked.
    function openStockIn(id){
      document.getElementById('stockInRows').innerHTML = '';
      stockInRowCount = 0;
      addStockInRow();
      if (id) {
        const select = document.querySelector('.stock-in-row select[name*="[inventory_item_id]"]');
        select.value = id;
        onStockInItemChange(select);
      }
      recalcStockInTotals();
      openModal('stockInModal');
    }

    addStockInRow();

    @if ($restockItemId)
      openStockIn({{ $restockItemId }});
    @elseif ($openForm)
      openStockIn();
    @elseif ($errors->has('purchase_source') || $errors->has('items.0.unit_cost') || $errors->has('invoice_number') || $errors->has('tax_rate'))
      openModal('stockInModal');
    @endif
  </script>

  @include('admin.partials.toasts')

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Inventory</title>
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
          'soft-btn-mint': '0 5px 0 #3F7457, 0 10px 18px rgba(111,174,139,0.3)',
          'soft-btn-coral': '0 5px 0 #9C4A41, 0 10px 18px rgba(224,119,107,0.3)',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex">

  @include('admin.partials.sidebar', ['active' => 'inventory'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Inventory</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Procurement stock and supplier orders for cups &amp; straws</p>
      </div>
      <a href="{{ route('admin.inventory-items.create') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Item
      </a>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total SKUs</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $procurement->count() + $supplier->count() }}</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Low Stock</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $lowStockCount }}</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock In Logged</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $stockInCount }}</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock Out Logged</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $stockOutCount }}</p></div>
      </div>
    </div>

    <!-- Category tabs -->
    <div class="flex items-center gap-2 mb-5 flex-wrap">
      <button id="tab-procurement" onclick="showCategory('procurement')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-stamp-500 text-cream-50 shadow-soft-btn">📦 Procurement</button>
      <button id="tab-supplier" onclick="showCategory('supplier')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🥤 Cups &amp; Straws (Supplier)</button>
    </div>
    <p id="categoryHint" class="text-[11px] text-stamp-300 font-semibold mb-4">Ingredients and general supplies the admin buys directly for the shop.</p>

    <!-- Item grids -->
    <div id="grid-procurement" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      @forelse ($procurement as $item)
        @include('admin.partials.inventory-item-card', ['item' => $item])
      @empty
        <p class="text-sm font-semibold text-stamp-300 py-6 col-span-full">No procurement items yet. Click "Add Item" to create one.</p>
      @endforelse
    </div>
    <div id="grid-supplier" class="hidden grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      @forelse ($supplier as $item)
        @include('admin.partials.inventory-item-card', ['item' => $item])
      @empty
        <p class="text-sm font-semibold text-stamp-300 py-6 col-span-full">No supplier items yet. Click "Add Item" to create one.</p>
      @endforelse
    </div>

    <!-- Activity log -->
    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-4">Recent Stock Activity</h3>
      <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-3">Date</th>
            <th class="py-2 px-3">Item</th>
            <th class="py-2 px-3">Type</th>
            <th class="py-2 px-3">Qty</th>
            <th class="py-2 px-3">Reason / Note</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recentTransactions as $transaction)
            <tr class="border-b border-cream-200 last:border-0">
              <td class="py-3 px-3 text-stamp-500 text-sm whitespace-nowrap">{{ $transaction->inventory_transaction_date->format('M j, Y') }}</td>
              <td class="py-3 px-3 font-bold text-stamp-700 text-sm">{{ $transaction->inventoryItem->name ?? 'Unknown item' }}</td>
              <td class="py-3 px-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $transaction->transaction_type === 'Restock' ? 'bg-mint-50 text-mint-600' : 'bg-coral-50 text-coral-600' }}">{{ $transaction->transaction_type }}</span>
              </td>
              <td class="py-3 px-3 font-display font-bold text-stamp-700 text-sm">{{ $transaction->transaction_type === 'Restock' ? '+' : '−' }}{{ rtrim(rtrim(number_format($transaction->quantity, 2), '0'), '.') }} {{ $transaction->inventoryItem->unit ?? '' }}</td>
              <td class="py-3 px-3 text-stamp-400 text-xs">{{ $transaction->reason ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="py-8 text-center text-stamp-300 text-sm">No stock movements yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </main>


  <!-- Stock In Modal (multi-line invoice) -->
  <div id="stockInModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-2xl p-7 relative max-h-[92vh] overflow-y-auto">
      <button type="button" onclick="closeModal('stockInModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-mint-50 shadow-soft-inset mx-auto flex items-center justify-center text-mint-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Stock In — Cash Invoice</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">Log a purchase receipt and receive stock in one go</p>
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
          Confirm Stock In &amp; Log Expense
        </button>
      </form>
    </div>
  </div>

  <!-- Stock Out Modal -->
  <div id="stockOutModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button type="button" onclick="closeModal('stockOutModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-coral-50 shadow-soft-inset mx-auto flex items-center justify-center text-coral-500 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Stock Out</h3>
        <p id="stockOutItemLabel" class="text-xs text-stamp-400 font-semibold mt-1">Removing stock for —</p>
      </div>
      <form method="POST" action="{{ route('admin.inventory-transactions.stock-out') }}" class="space-y-4">
        @csrf
        <input type="hidden" id="stockOutItemId" name="inventory_item_id" value="{{ old('inventory_item_id') }}">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Quantity Removed</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5 flex items-center gap-2">
            <input id="stockOutQty" type="number" min="0.01" step="0.01" name="quantity" value="{{ old('quantity') }}" placeholder="e.g. 5" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            <select id="stockOutUnit" name="quantity_unit" class="bg-cream-50 rounded-xl px-2 py-1 text-xs font-bold text-stamp-600 outline-none shrink-0">
              <option value="base">base</option>
              <option value="secondary" id="stockOutSecondaryOption" class="hidden">secondary</option>
            </select>
          </div>
          <p id="stockOutAvailable" class="text-[11px] text-stamp-300 font-semibold mt-1 ml-1">Available: 0</p>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Type</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="transaction_type" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="Waste" @selected(old('transaction_type', 'Waste') === 'Waste')>Waste / Spoiled / Damaged</option>
              <option value="Adjustment" @selected(old('transaction_type') === 'Adjustment')>Adjustment / Correction</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Date</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="date" name="inventory_transaction_date" value="{{ old('inventory_transaction_date', now()->toDateString()) }}" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reason (optional)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="reason" value="{{ old('reason') }}" placeholder="e.g. Broken during delivery" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-coral-500 to-coral-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn-coral active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
          Confirm Stock Out
        </button>
      </form>
    </div>
  </div>

  @php
    $inventoryItemsForJs = $procurement->concat($supplier)->map(function ($item) {
      return [
        'id' => $item->id,
        'name' => $item->name,
        'unit' => $item->unit,
        'secondary_unit' => $item->secondary_unit,
        'conversion_factor' => $item->conversion_factor,
      ];
    })->values();
  @endphp
  <script>
    const INVENTORY_ITEMS = @json($inventoryItemsForJs);

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
            ${options}
          </select>
          <input type="number" min="0.01" step="0.01" name="items[${index}][quantity]" placeholder="Qty" oninput="recalcStockInTotals()" class="col-span-2 bg-cream-50 rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
          <select name="items[${index}][quantity_unit]" class="stock-in-unit col-span-2 bg-cream-50 rounded-xl px-1 py-2 text-[11px] font-bold text-stamp-700 outline-none">
            <option value="base">base</option>
          </select>
          <input type="number" min="0" step="0.01" name="items[${index}][unit_cost]" placeholder="Cost" oninput="recalcStockInTotals()" class="col-span-2 bg-cream-50 rounded-xl px-2 py-2 text-xs font-bold text-stamp-700 outline-none">
          <span class="stock-in-amount col-span-1 text-xs font-display font-bold text-stamp-700 text-right">₱0</span>
          <button type="button" onclick="removeStockInRow(this)" class="col-span-1 text-coral-500 hover:text-coral-600 flex justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>`;
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
      const unitSelect = row.querySelector('.stock-in-unit');
      const item = INVENTORY_ITEMS.find(i => String(i.id) === select.value);
      unitSelect.innerHTML = '<option value="base">' + (item ? item.unit : 'base') + '</option>';
      if (item && item.secondary_unit && item.conversion_factor) {
        unitSelect.insertAdjacentHTML('beforeend', `<option value="secondary">${item.secondary_unit}</option>`);
      }
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

    const CATEGORY_HINTS = {
      procurement: 'Ingredients and general supplies the admin buys directly for the shop.',
      supplier: 'Only cups and straws are ordered through the packaging supplier.',
    };

    function showCategory(cat){
      ['procurement', 'supplier'].forEach(c => {
        const tab = document.getElementById('tab-' + c);
        tab.classList.toggle('bg-stamp-500', c === cat);
        tab.classList.toggle('text-cream-50', c === cat);
        tab.classList.toggle('shadow-soft-btn', c === cat);
        tab.classList.toggle('bg-cream-100', c !== cat);
        tab.classList.toggle('text-stamp-600', c !== cat);
        document.getElementById('grid-' + c).classList.toggle('hidden', c !== cat);
      });
      document.getElementById('categoryHint').textContent = CATEGORY_HINTS[cat];
    }

    function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

    function openStockIn(id, name){
      document.getElementById('stockInRows').innerHTML = '';
      stockInRowCount = 0;
      addStockInRow();
      const firstRow = document.querySelector('.stock-in-row');
      const select = firstRow.querySelector('select[name*="[inventory_item_id]"]');
      select.value = id;
      onStockInItemChange(select);
      recalcStockInTotals();
      openModal('stockInModal');
    }

    function openStockOut(id, name, quantity, unit){
      document.getElementById('stockOutItemId').value = id;
      document.getElementById('stockOutItemLabel').textContent = 'Removing stock for ' + name;
      document.getElementById('stockOutAvailable').textContent = 'Available: ' + quantity + ' ' + unit;

      const item = INVENTORY_ITEMS.find(i => String(i.id) === String(id));
      const secondaryOption = document.getElementById('stockOutSecondaryOption');
      const unitSelect = document.getElementById('stockOutUnit');
      unitSelect.value = 'base';
      if (item && item.secondary_unit && item.conversion_factor) {
        secondaryOption.textContent = item.secondary_unit;
        secondaryOption.classList.remove('hidden');
      } else {
        secondaryOption.classList.add('hidden');
      }

      openModal('stockOutModal');
    }

    addStockInRow();

    @if ($errors->has('purchase_source') || $errors->has('items.0.unit_cost') || $errors->has('invoice_number') || $errors->has('tax_rate'))
      openModal('stockInModal');
    @elseif ($errors->has('transaction_type') || $errors->has('quantity'))
      openModal('stockOutModal');
    @endif
  </script>

</body>
</html>

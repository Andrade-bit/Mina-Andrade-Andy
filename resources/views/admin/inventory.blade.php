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
        <p class="text-stamp-500 text-sm font-semibold mt-1">What is already in stock. New items and restocks are recorded as purchases in Supply Purchases.</p>
      </div>
      <a href="{{ route('admin.supply-purchases.index', ['record' => 1]) }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Record Purchase
      </a>
    </div>

    <!-- Stat cards: each one opens the list behind its number -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <a href="{{ route('admin.inventory') }}#items" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform focus-visible:ring-2 focus-visible:ring-stamp-300 outline-none">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total SKUs</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $totalItems }}</p></div>
      </a>
      <a href="{{ route('admin.inventory', ['stock' => 'low']) }}#items" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform focus-visible:ring-2 focus-visible:ring-stamp-300 outline-none">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Low Stock</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $lowStockCount }}</p></div>
      </a>
      <a href="{{ route('admin.supply-purchases.index') }}" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform focus-visible:ring-2 focus-visible:ring-stamp-300 outline-none">
        <div class="w-12 h-12 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock In Logged</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $stockInCount }}</p></div>
      </a>
      <a href="{{ route('admin.inventory-transactions.index') }}" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform focus-visible:ring-2 focus-visible:ring-stamp-300 outline-none">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock Out Logged</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $stockOutCount }}</p></div>
      </a>
    </div>

    <!-- Category tabs -->
    <div id="items" class="scroll-mt-20"></div>
    @if ($onlyLow)
      <div class="mb-4 flex items-center justify-between gap-3 flex-wrap rounded-2xl bg-coral-50 px-4 py-3 text-sm font-bold text-coral-600">
        <span>Showing only items at or below their reorder level.</span>
        <a href="{{ route('admin.inventory') }}#items" class="underline">Show all items</a>
      </div>
    @endif
    <div class="flex items-center gap-2 mb-5 flex-wrap">
      <button id="tab-procurement" onclick="showCategory('procurement')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-stamp-500 text-cream-50 shadow-soft-btn">📦 Ingredients</button>
      <button id="tab-supplier" onclick="showCategory('supplier')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🥤 Cups &amp; Straws</button>
      <button id="tab-archived" onclick="showCategory('archived')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🗄️ Archived ({{ $archived->count() }})</button>
    </div>
    <p id="categoryHint" class="text-[11px] text-stamp-300 font-semibold mb-4">Ingredients and general supplies you keep in stock. Use Restock on a card, or Supply Purchases, to buy more.</p>

    <!-- Item grids -->
    <div id="grid-procurement" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      @forelse ($procurement as $item)
        @include('admin.partials.inventory-item-card', ['item' => $item])
      @empty
        <p class="text-sm font-semibold text-stamp-300 py-6 col-span-full">{{ $onlyLow ? "No ingredients are low on stock." : "No ingredients yet. Record a purchase to add one." }}</p>
      @endforelse
    </div>
    <div id="grid-supplier" class="hidden grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      @forelse ($supplier as $item)
        @include('admin.partials.inventory-item-card', ['item' => $item])
      @empty
        <p class="text-sm font-semibold text-stamp-300 py-6 col-span-full">{{ $onlyLow ? "No cups or straws are low on stock." : "No cups or straws yet. Record a purchase to add one." }}</p>
      @endforelse
    </div>

    <div id="grid-archived" class="hidden grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      @forelse ($archived as $item)
        @include('admin.partials.inventory-item-card', ['item' => $item])
      @empty
        <p class="text-sm font-semibold text-stamp-300 py-6 col-span-full">Nothing archived. Items you mark as unused show up here so you can restore them.</p>
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

  <script>
    const INVENTORY_ITEMS = @json($itemOptions);

    const CATEGORY_HINTS = {
      procurement: 'Ingredients and general supplies you keep in stock. Use Restock on a card, or Supply Purchases, to buy more.',
      supplier: 'Cups and straws. Buy more through Supply Purchases.',
      archived: 'Unused items. They are hidden from Stock In, recipes and low-stock alerts, and keep their history.',
    };

    function showCategory(cat){
      ['procurement', 'supplier', 'archived'].forEach(c => {
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


    @if ($errors->has('transaction_type') || $errors->has('quantity'))
      openModal('stockOutModal');
    @endif
  </script>

  @include('admin.partials.toasts')

</body>
</html>

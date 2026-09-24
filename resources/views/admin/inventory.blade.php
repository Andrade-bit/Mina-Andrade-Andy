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

  <!-- Mobile top bar -->
  <div class="md:hidden fixed top-0 inset-x-0 z-30 bg-white shadow-soft-sm px-4 py-3 flex items-center gap-3">
    <button onclick="openSidebar()" class="w-10 h-10 rounded-xl bg-cream-100 flex items-center justify-center text-stamp-600 shrink-0">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="font-display font-bold text-stamp-700">Catbrews</p>
  </div>

  <!-- Sidebar backdrop (mobile) -->
  <div id="sidebarBackdrop" onclick="closeSidebar()" class="hidden md:hidden fixed inset-0 bg-stamp-700/40 z-40"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 flex flex-col w-72 md:w-64 bg-white md:bg-white/70 shadow-soft m-0 md:m-4 rounded-none md:rounded-[2rem] p-6 shrink-0 -translate-x-full md:translate-x-0 transition-transform duration-300 overflow-y-auto">
    <button onclick="closeSidebar()" class="md:hidden absolute top-4 right-4 text-stamp-300 hover:text-stamp-600">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="flex flex-col items-center mb-8">
      <div class="w-16 h-16 rounded-full bg-cream-100 shadow-soft-inset ring-4 ring-white flex items-center justify-center text-stamp-600 mb-2">
        <svg viewBox="0 0 64 64" class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 24 L16 10 L28 20"/>
          <path d="M44 24 L48 10 L36 20"/>
          <path d="M24 16 Q32 8 40 16"/>
          <circle cx="32" cy="8" r="3.2"/>
          <circle cx="32" cy="36" r="17"/>
          <circle cx="26" cy="34" r="1.6" fill="currentColor" stroke="none"/>
          <circle cx="38" cy="34" r="1.6" fill="currentColor" stroke="none"/>
          <path d="M30 40 Q32 42 34 40"/>
          <path d="M9 32 L19 34 M9 38 L19 36" opacity="0.6"/>
          <path d="M55 32 L45 34 M55 38 L45 36" opacity="0.6"/>
        </svg>
      </div>
      <h1 class="font-display font-bold text-xl text-stamp-700">Catbrews</h1>
      <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-stamp-300">Admin Panel</p>
    </div>

    <nav class="flex-1 space-y-1.5">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>
      <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
        User Management
      </a>
      <a href="{{ route('pos.login') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-9 4h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/></svg>
        POS Terminal
      </a>
      <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Products
      </a>
      <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-cream-50 bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        Inventory
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5-1a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm11 6.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
        Orders
      </a>
      <a href="{{ route('pos.transactions') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Reports
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    </nav>

    <div class="pt-4 mt-auto border-t border-cream-200 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 font-display font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="text-sm flex-1">
        <p class="font-bold text-stamp-700 leading-tight">{{ auth()->user()->name }}</p>
        <p class="text-stamp-300 text-xs font-bold">Owner</p>
      </div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" title="Log out" class="text-stamp-300 hover:text-stamp-600">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Inventory</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Procurement stock and supplier orders for cups &amp; straws</p>
      </div>
      <button onclick="openAddItem()" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Item
      </button>
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
              <td class="py-3 px-3 font-bold text-stamp-700 text-sm">{{ $transaction->inventoryItem->name ?? 'Deleted item' }}</td>
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

  <!-- Add Item Modal -->
  <div id="addItemModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button type="button" onclick="closeModal('addItemModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-stamp-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Add Inventory Item</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">Create a new SKU to track stock for</p>
      </div>
      <form method="POST" action="{{ route('admin.inventory-items.store') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Item Name</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Vanilla Syrup" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Category</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <select id="addItemType" name="type" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                <option value="ingredient" @selected(old('type', 'ingredient') === 'ingredient')>Procurement</option>
                <option value="supply" @selected(old('type') === 'supply')>Supplier (Cups/Straws)</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Unit</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="text" name="unit" value="{{ old('unit') }}" placeholder="e.g. kg, liters, pcs" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Starting Quantity</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="number" min="0" step="0.01" name="current_quantity" value="{{ old('current_quantity', 0) }}" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reorder Level</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="number" min="0" step="0.01" name="reorder_level" value="{{ old('reorder_level', 0) }}" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Create Item
        </button>
      </form>
    </div>
  </div>

  <!-- Stock In Modal -->
  <div id="stockInModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button type="button" onclick="closeModal('stockInModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-mint-50 shadow-soft-inset mx-auto flex items-center justify-center text-mint-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Stock In</h3>
        <p id="stockInItemLabel" class="text-xs text-stamp-400 font-semibold mt-1">Receiving stock for —</p>
      </div>
      <form method="POST" action="{{ route('admin.supply-purchases.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" id="stockInItemId" name="items[0][inventory_item_id]" value="{{ old('items.0.inventory_item_id') }}">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Quantity Received</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockInQty" type="number" min="0.01" step="0.01" name="items[0][quantity]" value="{{ old('items.0.quantity') }}" placeholder="e.g. 20" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Where did you buy this?</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="purchase_source" value="{{ old('purchase_source', 'NCCC Mall') }}" placeholder="e.g. NCCC Mall" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">Used when you personally buy stock because the supplier can't deliver.</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Unit Cost (₱)</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="number" min="0" step="0.01" name="items[0][unit_cost]" value="{{ old('items.0.unit_cost') }}" placeholder="0.00" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
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
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Paid Via</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="payment_method" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="Cash" @selected(old('payment_method', 'Cash') === 'Cash')>Cash</option>
              <option value="Gcash" @selected(old('payment_method') === 'Gcash')>GCash</option>
              <option value="Card" @selected(old('payment_method') === 'Card')>Card</option>
            </select>
          </div>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-mint-500 to-mint-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn-mint active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Confirm Stock In
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
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockOutQty" type="number" min="0.01" step="0.01" name="quantity" value="{{ old('quantity') }}" placeholder="e.g. 5" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
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
      document.getElementById('addItemType').value = cat === 'supplier' ? 'supply' : 'ingredient';
    }

    function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

    function openAddItem(){ openModal('addItemModal'); }

    function openStockIn(id, name){
      document.getElementById('stockInItemId').value = id;
      document.getElementById('stockInItemLabel').textContent = 'Receiving stock for ' + name;
      openModal('stockInModal');
    }

    function openStockOut(id, name, quantity, unit){
      document.getElementById('stockOutItemId').value = id;
      document.getElementById('stockOutItemLabel').textContent = 'Removing stock for ' + name;
      document.getElementById('stockOutAvailable').textContent = 'Available: ' + quantity + ' ' + unit;
      openModal('stockOutModal');
    }

    @if ($errors->has('current_quantity') || $errors->has('reorder_level'))
      openModal('addItemModal');
    @elseif ($errors->has('purchase_source') || $errors->has('items.0.unit_cost'))
      openModal('stockInModal');
    @elseif ($errors->has('transaction_type') || $errors->has('quantity'))
      openModal('stockOutModal');
    @endif
  </script>

  <script>
    function openSidebar(){
      document.getElementById('sidebar').classList.remove('-translate-x-full');
      document.getElementById('sidebarBackdrop').classList.remove('hidden');
    }
    function closeSidebar(){
      document.getElementById('sidebar').classList.add('-translate-x-full');
      document.getElementById('sidebarBackdrop').classList.add('hidden');
    }
  </script>

</body>
</html>

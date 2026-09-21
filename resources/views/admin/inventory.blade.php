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
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
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
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
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
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Reports
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    </nav>

    <div class="pt-4 mt-auto border-t border-cream-200 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 font-display font-bold text-sm">A</div>
      <div class="text-sm flex-1">
        <p class="font-bold text-stamp-700 leading-tight">Admin</p>
        <p class="text-stamp-300 text-xs font-bold">Owner</p>
      </div>
      <a href="{{ route('admin.login') }}" title="Log out" class="text-stamp-300 hover:text-stamp-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Inventory</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Procurement stock and supplier orders for cups &amp; straws</p>
      </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total SKUs</p><p id="statTotalSkus" class="font-display font-bold text-2xl text-stamp-700">0</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Low Stock</p><p id="statLowStock" class="font-display font-bold text-2xl text-stamp-700">0</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock In Logged</p><p id="statStockIn" class="font-display font-bold text-2xl text-stamp-700">0</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Stock Out Logged</p><p id="statStockOut" class="font-display font-bold text-2xl text-stamp-700">0</p></div>
      </div>
    </div>

    <!-- Category tabs -->
    <div class="flex items-center gap-2 mb-5 flex-wrap">
      <button id="tab-procurement" onclick="showCategory('procurement')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-stamp-500 text-cream-50 shadow-soft-btn">📦 Procurement</button>
      <button id="tab-supplier" onclick="showCategory('supplier')" class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🥤 Cups &amp; Straws (Supplier)</button>
    </div>
    <p id="categoryHint" class="text-[11px] text-stamp-300 font-semibold mb-4">Ingredients and general supplies the admin buys directly for the shop.</p>

    <!-- Item grid -->
    <div id="itemGrid" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8"></div>

    <!-- Activity log -->
    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-4">Recent Stock Activity</h3>
      <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-3">Date</th>
            <th class="py-2 px-3">Item</th>
            <th class="py-2 px-3">Category</th>
            <th class="py-2 px-3">Type</th>
            <th class="py-2 px-3">Qty</th>
            <th class="py-2 px-3">Supplier / Reason</th>
            <th class="py-2 px-3">Note</th>
          </tr>
        </thead>
        <tbody id="logBody">
          <tr><td colspan="7" class="py-8 text-center text-stamp-300 text-sm">No stock movements yet.</td></tr>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Stock In Modal -->
  <div id="stockInModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button onclick="closeModal('stockInModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-mint-50 shadow-soft-inset mx-auto flex items-center justify-center text-mint-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Stock In</h3>
        <p id="stockInItemLabel" class="text-xs text-stamp-400 font-semibold mt-1">Receiving stock for —</p>
      </div>
      <form class="space-y-4" onsubmit="event.preventDefault(); submitStockIn();">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Quantity Received</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockInQty" type="number" min="1" step="1" placeholder="e.g. 20" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Supplier / Vendor</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockInParty" type="text" placeholder="e.g. Davao Coffee Traders" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Unit Cost (₱)</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input id="stockInCost" type="number" min="0" step="0.01" placeholder="0.00" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Date</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input id="stockInDate" type="date" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Note (optional)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockInNote" type="text" placeholder="e.g. Weekly delivery" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
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
      <button onclick="closeModal('stockOutModal')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-coral-50 shadow-soft-inset mx-auto flex items-center justify-center text-coral-500 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Stock Out</h3>
        <p id="stockOutItemLabel" class="text-xs text-stamp-400 font-semibold mt-1">Removing stock for —</p>
      </div>
      <form class="space-y-4" onsubmit="event.preventDefault(); submitStockOut();">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Quantity Removed</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockOutQty" type="number" min="1" step="1" placeholder="e.g. 5" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
          <p id="stockOutAvailable" class="text-[11px] text-stamp-300 font-semibold mt-1 ml-1">Available: 0</p>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reason</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select id="stockOutReason" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option>Used in Production</option>
              <option>Used in Store</option>
              <option>Damaged / Defective</option>
              <option>Spoiled / Expired</option>
              <option>Wastage</option>
              <option>Other</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Date</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockOutDate" type="date" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Note (optional)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input id="stockOutNote" type="text" placeholder="e.g. Broken during delivery" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <p id="stockOutError" class="hidden text-xs font-bold text-coral-500 bg-coral-50 rounded-lg px-3 py-2">Quantity exceeds available stock.</p>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-coral-500 to-coral-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn-coral active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
          Confirm Stock Out
        </button>
      </form>
    </div>
  </div>

  <script>
    const DEFAULT_INVENTORY = {
      procurement: [
        { id: 'coffee-beans', name: 'Coffee Beans', unit: 'kg', stock: 25, reorder: 10 },
        { id: 'fresh-milk', name: 'Fresh Milk', unit: 'liters', stock: 40, reorder: 15 },
        { id: 'condensed-milk', name: 'Condensed Milk', unit: 'cans', stock: 30, reorder: 12 },
        { id: 'caramel-syrup', name: 'Caramel Syrup', unit: 'bottles', stock: 8, reorder: 5 },
        { id: 'matcha-powder', name: 'Matcha Powder', unit: 'kg', stock: 4, reorder: 3 },
        { id: 'chocolate-powder', name: 'Chocolate Powder', unit: 'kg', stock: 12, reorder: 5 },
        { id: 'mango-puree', name: 'Mango Puree', unit: 'liters', stock: 10, reorder: 6 },
        { id: 'lemon-concentrate', name: 'Lemon Juice Concentrate', unit: 'liters', stock: 6, reorder: 4 },
        { id: 'white-sugar', name: 'White Sugar', unit: 'kg', stock: 20, reorder: 8 },
        { id: 'strawberry-syrup', name: 'Strawberry Syrup', unit: 'bottles', stock: 7, reorder: 5 },
      ],
      supplier: [
        { id: 'cup-small', name: 'Small Cups (12oz)', unit: 'pcs', stock: 250, reorder: 100 },
        { id: 'cup-medium', name: 'Medium Cups (16oz)', unit: 'pcs', stock: 180, reorder: 100 },
        { id: 'cup-large', name: 'Large Cups (22oz)', unit: 'pcs', stock: 90, reorder: 100 },
        { id: 'straws', name: 'Plastic Straws', unit: 'pcs', stock: 400, reorder: 150 },
      ],
    };

    const CATEGORY_HINTS = {
      procurement: 'Ingredients and general supplies the admin buys directly for the shop.',
      supplier: 'Only cups and straws are ordered through the packaging supplier.',
    };

    let inventory = JSON.parse(localStorage.getItem('cb_inventory') || 'null') || JSON.parse(JSON.stringify(DEFAULT_INVENTORY));
    let logs = JSON.parse(localStorage.getItem('cb_inventory_log') || '[]');
    let activeCategory = 'procurement';
    let pendingItem = null;

    function saveInventory(){
      localStorage.setItem('cb_inventory', JSON.stringify(inventory));
      localStorage.setItem('cb_inventory_log', JSON.stringify(logs));
    }

    function findItem(category, id){
      return inventory[category].find(i => i.id === id);
    }

    function showCategory(cat){
      activeCategory = cat;
      ['procurement', 'supplier'].forEach(c => {
        const tab = document.getElementById('tab-' + c);
        tab.classList.toggle('bg-stamp-500', c === cat);
        tab.classList.toggle('text-cream-50', c === cat);
        tab.classList.toggle('shadow-soft-btn', c === cat);
        tab.classList.toggle('bg-cream-100', c !== cat);
        tab.classList.toggle('text-stamp-600', c !== cat);
      });
      document.getElementById('categoryHint').textContent = CATEGORY_HINTS[cat];
      renderItems();
    }

    function renderItems(){
      const grid = document.getElementById('itemGrid');
      grid.innerHTML = inventory[activeCategory].map(item => {
        const low = item.stock <= item.reorder;
        return `
          <div class="bg-white rounded-3xl shadow-soft p-5">
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="font-extrabold text-stamp-700">${item.name}</p>
                <p class="text-[11px] text-stamp-300 font-bold uppercase tracking-wide">Reorder at ${item.reorder} ${item.unit}</p>
              </div>
              ${low ? '<span class="bg-coral-50 text-coral-600 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full">Low Stock</span>' : ''}
            </div>
            <p class="font-display font-bold text-3xl text-stamp-700 mb-4">${item.stock} <span class="text-sm font-body font-bold text-stamp-300">${item.unit}</span></p>
            <div class="flex gap-2">
              <button onclick="openStockIn('${activeCategory}','${item.id}')" class="flex-1 py-2.5 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
                Stock In
              </button>
              <button onclick="openStockOut('${activeCategory}','${item.id}')" class="flex-1 py-2.5 rounded-xl bg-coral-50 hover:bg-coral-500 hover:text-cream-50 text-coral-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
                Stock Out
              </button>
            </div>
          </div>
        `;
      }).join('');
      updateStats();
    }

    function updateStats(){
      const all = [...inventory.procurement, ...inventory.supplier];
      document.getElementById('statTotalSkus').textContent = all.length;
      document.getElementById('statLowStock').textContent = all.filter(i => i.stock <= i.reorder).length;
      document.getElementById('statStockIn').textContent = logs.filter(l => l.type === 'in').length;
      document.getElementById('statStockOut').textContent = logs.filter(l => l.type === 'out').length;
    }

    function todayStr(){ return new Date().toISOString().slice(0, 10); }

    function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

    function openStockIn(category, id){
      pendingItem = { category, id };
      const item = findItem(category, id);
      document.getElementById('stockInItemLabel').textContent = 'Receiving stock for ' + item.name;
      document.getElementById('stockInQty').value = '';
      document.getElementById('stockInParty').value = '';
      document.getElementById('stockInCost').value = '';
      document.getElementById('stockInNote').value = '';
      document.getElementById('stockInDate').value = todayStr();
      openModal('stockInModal');
    }

    function openStockOut(category, id){
      pendingItem = { category, id };
      const item = findItem(category, id);
      document.getElementById('stockOutItemLabel').textContent = 'Removing stock for ' + item.name;
      document.getElementById('stockOutAvailable').textContent = 'Available: ' + item.stock + ' ' + item.unit;
      document.getElementById('stockOutQty').value = '';
      document.getElementById('stockOutNote').value = '';
      document.getElementById('stockOutDate').value = todayStr();
      document.getElementById('stockOutError').classList.add('hidden');
      openModal('stockOutModal');
    }

    function submitStockIn(){
      if (!pendingItem) return;
      const item = findItem(pendingItem.category, pendingItem.id);
      const qty = parseInt(document.getElementById('stockInQty').value, 10);
      if (!qty || qty <= 0) return;
      item.stock += qty;
      logs.unshift({
        date: document.getElementById('stockInDate').value || todayStr(),
        category: pendingItem.category,
        itemName: item.name,
        type: 'in',
        qty,
        unit: item.unit,
        party: document.getElementById('stockInParty').value || '—',
        cost: parseFloat(document.getElementById('stockInCost').value) || 0,
        note: document.getElementById('stockInNote').value || '',
      });
      saveInventory();
      renderItems();
      renderLog();
      closeModal('stockInModal');
    }

    function submitStockOut(){
      if (!pendingItem) return;
      const item = findItem(pendingItem.category, pendingItem.id);
      const qty = parseInt(document.getElementById('stockOutQty').value, 10);
      if (!qty || qty <= 0) return;
      if (qty > item.stock) {
        document.getElementById('stockOutError').classList.remove('hidden');
        return;
      }
      item.stock -= qty;
      logs.unshift({
        date: document.getElementById('stockOutDate').value || todayStr(),
        category: pendingItem.category,
        itemName: item.name,
        type: 'out',
        qty,
        unit: item.unit,
        party: document.getElementById('stockOutReason').value,
        note: document.getElementById('stockOutNote').value || '',
      });
      saveInventory();
      renderItems();
      renderLog();
      closeModal('stockOutModal');
    }

    function renderLog(){
      const body = document.getElementById('logBody');
      if (logs.length === 0) {
        body.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-stamp-300 text-sm">No stock movements yet.</td></tr>';
        return;
      }
      body.innerHTML = logs.slice(0, 30).map(l => `
        <tr class="border-b border-cream-200 last:border-0">
          <td class="py-3 px-3 text-stamp-500 text-sm whitespace-nowrap">${l.date}</td>
          <td class="py-3 px-3 font-bold text-stamp-700 text-sm">${l.itemName}</td>
          <td class="py-3 px-3">
            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full ${l.category === 'supplier' ? 'bg-stamp-100 text-stamp-600' : 'bg-cream-100 text-stamp-600'}">${l.category === 'supplier' ? 'Supplier' : 'Procurement'}</span>
          </td>
          <td class="py-3 px-3">
            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full ${l.type === 'in' ? 'bg-mint-50 text-mint-600' : 'bg-coral-50 text-coral-600'}">${l.type === 'in' ? 'Stock In' : 'Stock Out'}</span>
          </td>
          <td class="py-3 px-3 font-display font-bold text-stamp-700 text-sm">${l.type === 'in' ? '+' : '−'}${l.qty} ${l.unit}</td>
          <td class="py-3 px-3 text-stamp-500 text-sm">${l.party}</td>
          <td class="py-3 px-3 text-stamp-400 text-xs">${l.note || '—'}</td>
        </tr>
      `).join('');
    }

    showCategory('procurement');
    renderLog();
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

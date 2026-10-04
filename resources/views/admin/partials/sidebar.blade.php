@php
  $navActive = $active ?? '';
  $navClass = fn (string $key) => $navActive === $key
      ? 'flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-cream-50 bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn'
      : 'flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors';
@endphp

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
  <div class="flex flex-col items-center mb-6">
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

  <nav class="flex-1 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="{{ $navClass('dashboard') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      Dashboard
    </a>

    <p class="text-[10px] font-extrabold uppercase tracking-widest text-stamp-300 px-4 pt-3 pb-1">Menu</p>
    <a href="{{ route('admin.products.index') }}" class="{{ $navClass('products') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      Products
    </a>
    <a href="{{ route('admin.product-categories.index') }}" class="{{ $navClass('product-categories') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-4-7 4V5z"/></svg>
      Categories
    </a>
    <a href="{{ route('admin.cup-sizes.index') }}" class="{{ $navClass('cup-sizes') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-6 0v4.5L4.5 15A2 2 0 006.28 18h11.44a2 2 0 001.78-3L15 7.5V3"/></svg>
      Cup Sizes
    </a>

    <p class="text-[10px] font-extrabold uppercase tracking-widest text-stamp-300 px-4 pt-3 pb-1">Inventory</p>
    <a href="{{ route('admin.inventory') }}" class="{{ $navClass('inventory') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
      <span class="flex-1">Inventory</span>
      @if ($sidebarLowStockCount > 0)
        <span class="shrink-0 min-w-[20px] h-5 px-1.5 rounded-full bg-coral-500 text-cream-50 text-[10px] font-extrabold flex items-center justify-center" title="{{ $sidebarLowStockCount }} item(s) need reordering">{{ $sidebarLowStockCount }}</span>
      @endif
    </a>
    <a href="{{ route('admin.suppliers.index') }}" class="{{ $navClass('suppliers') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
      Suppliers
    </a>
    <a href="{{ route('admin.supply-purchases.index') }}" class="{{ $navClass('supply-purchases') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 5h13M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
      Supply Purchases
    </a>

    <p class="text-[10px] font-extrabold uppercase tracking-widest text-stamp-300 px-4 pt-3 pb-1">Sales</p>
    <a href="{{ route('pos.login') }}" class="{{ $navClass('pos') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-9 4h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/></svg>
      POS Terminal
    </a>
    <a href="{{ route('pos.transactions') }}" class="{{ $navClass('orders') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5-1a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm11 6.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
      Orders
    </a>
    <a href="{{ route('admin.promos.index') }}" class="{{ $navClass('promos') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-5.586 5.586a1 1 0 01-1.414 0L6.293 10.293A1 1 0 016 9.586V4a1 1 0 011-1z"/></svg>
      Promos
    </a>

    <p class="text-[10px] font-extrabold uppercase tracking-widest text-stamp-300 px-4 pt-3 pb-1">Finance</p>
    <a href="{{ route('admin.expenses.index') }}" class="{{ $navClass('expenses') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 012 2v16l-4-2-3 2-3-2-3 2-3-2-3 2V5a2 2 0 012-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 8h6M9 12h6M9 16h3"/></svg>
      Expenses
    </a>
    <a href="{{ route('admin.reports.index') }}" class="{{ $navClass('reports') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Reports
    </a>
    <a href="{{ route('admin.expense-categories.index') }}" class="{{ $navClass('expense-categories') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
      Expense Categories
    </a>

    <p class="text-[10px] font-extrabold uppercase tracking-widest text-stamp-300 px-4 pt-3 pb-1">Account</p>
    <a href="{{ route('admin.users') }}" class="{{ $navClass('users') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
      User Management
    </a>
    <a href="{{ route('admin.settings') }}" class="{{ $navClass('settings') }}">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Settings
    </a>
  </nav>

  <div class="pt-4 mt-4 border-t border-cream-200 flex items-center gap-3">
    <div class="w-10 h-10 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 font-display font-bold text-sm shrink-0">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
    <div class="text-sm flex-1 min-w-0">
      <p class="font-bold text-stamp-700 leading-tight truncate">{{ auth()->user()->name }}</p>
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

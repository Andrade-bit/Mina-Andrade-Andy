<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Products</title>
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
      <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-cream-50 bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Products
      </a>
      <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
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
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Products</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Your Catbrews menu, organized by category</p>
      </div>
      <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Product
      </a>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
      <div class="flex items-center gap-2 bg-white rounded-2xl shadow-soft-sm px-4 py-2.5 flex-1 min-w-[220px]">
        <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full bg-transparent outline-none text-sm text-stamp-700 placeholder-stamp-300 font-semibold">
      </div>
      <div class="bg-white rounded-2xl shadow-soft-sm px-4 py-2.5">
        <select name="category" onchange="this.form.submit()" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
          <option value="">All Categories</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->category_name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-2xl bg-stamp-500 hover:bg-stamp-600 text-cream-50 font-extrabold text-xs transition-colors">Search</button>
      @if (request('search') || request('category'))
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-500 font-extrabold text-xs transition-colors">Clear</a>
      @endif
    </form>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="grid gap-3">
        @forelse ($products as $product)
          <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap">
            <div class="w-11 h-11 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 shrink-0">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-[160px]">
              <p class="font-extrabold text-stamp-700">{{ $product->product_name }}</p>
              <p class="text-xs text-stamp-400 font-semibold">{{ $product->productCategory->category_name ?? 'Uncategorized' }}</p>
            </div>
            <div class="flex items-center gap-2">
              <a href="{{ route('admin.products.edit', $product) }}" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
              <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Remove {{ $product->product_name }} from the menu?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">
            @if (request('search') || request('category'))
              No products match your filters.
            @else
              No products yet. Click "Add Product" to build your menu.
            @endif
          </p>
        @endforelse
      </div>
      @include('admin.partials.pagination', ['paginator' => $products])
    </div>
  </main>

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

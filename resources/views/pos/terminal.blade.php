<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews POS Terminal</title>
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
        keyframes: {
          fadeUp: { '0%': { opacity: 0, transform: 'translateY(10px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
          popIn: { '0%': { opacity: 0, transform: 'scale(0.9)' }, '100%': { opacity: 1, transform: 'scale(1)' } },
          slideIn: { '0%': { opacity: 0, transform: 'translateX(12px)' }, '100%': { opacity: 1, transform: 'translateX(0)' } },
          bounceIn: { '0%': { opacity: 0, transform: 'scale(0.5)' }, '60%': { opacity: 1, transform: 'scale(1.15)' }, '100%': { opacity: 1, transform: 'scale(1)' } },
        },
        animation: {
          'fade-up': 'fadeUp 0.4s ease-out both',
          'pop-in': 'popIn 0.18s ease-out both',
          'slide-in': 'slideIn 0.25s ease-out both',
          'bounce-in': 'bounceIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700">

  <!-- Top bar -->
  <header class="bg-white shadow-soft-sm px-5 py-3 flex items-center justify-between flex-wrap gap-3 sticky top-0 z-30">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-cream-100 shadow-soft-inset flex items-center justify-center text-stamp-600 shrink-0">
        <svg viewBox="0 0 64 64" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 24 L16 10 L28 20"/><path d="M44 24 L48 10 L36 20"/><path d="M24 16 Q32 8 40 16"/>
          <circle cx="32" cy="8" r="3.2"/><circle cx="32" cy="36" r="17"/>
          <circle cx="26" cy="34" r="1.6" fill="currentColor" stroke="none"/><circle cx="38" cy="34" r="1.6" fill="currentColor" stroke="none"/>
          <path d="M30 40 Q32 42 34 40"/>
        </svg>
      </div>
      <div>
        <p class="font-display font-bold text-stamp-700 leading-tight">Catbrews POS</p>
        <p class="text-[11px] text-stamp-300 font-bold">New Sale</p>
      </div>
    </div>

    <div class="flex items-center gap-1 bg-cream-100 rounded-2xl p-1 shadow-soft-inset">
      <button id="typeDineIn" onclick="setOrderType('Dine In')" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all bg-stamp-500 text-cream-50">Dine In</button>
      <button id="typeTakeOut" onclick="setOrderType('Take Out')" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all text-stamp-500">Take Out</button>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('pos.transactions') }}" class="admin-only w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Transactions &amp; reports">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      </a>
      <a href="{{ route('admin.users') }}" class="admin-only w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Admin dashboard">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </a>

      <div class="hidden sm:flex items-center gap-1 bg-cream-100 rounded-full p-1 shadow-soft-inset" title="Design preview only — switches which role's view you're seeing">
        <button id="roleOwnerBtn" onclick="applyRole('owner')" class="px-3 py-1.5 rounded-full text-[11px] font-extrabold transition-all">Owner</button>
        <button id="roleStaffBtn" onclick="applyRole('staff')" class="px-3 py-1.5 rounded-full text-[11px] font-extrabold transition-all">Staff</button>
      </div>

      <div class="flex items-center gap-2 pl-2 border-l border-cream-200">
        <div id="userAvatar" class="w-9 h-9 rounded-full bg-stamp-500 text-cream-50 flex items-center justify-center font-display font-bold text-xs">A</div>
        <div class="hidden md:block">
          <p id="currentUserName" class="text-sm font-extrabold text-stamp-700 leading-tight">Admin</p>
          <p id="currentUserRole" class="text-[10px] font-bold text-stamp-300 uppercase tracking-wide">Owner</p>
        </div>
      </div>
      <a href="{{ route('pos.login') }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors" title="Switch user">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </header>

  <p class="admin-only text-center text-[11px] font-bold text-stamp-300 bg-cream-100 py-1.5">Owner/Staff toggle above is a design preview — it shows how the POS looks for each role, it isn't a real login switch.</p>

  <div class="flex flex-col lg:flex-row gap-5 p-5 max-w-7xl mx-auto">

    <!-- Products -->
    <div class="flex-1">
      <div class="flex items-center gap-2 mb-4 flex-wrap">
        <button id="tab-hot" onclick="showCategory('hot')" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-all bg-stamp-500 text-cream-50 shadow-soft-btn">🔥 Hot Coffee</button>
        <button id="tab-coffee" onclick="showCategory('coffee')" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🧊 Iced Coffee</button>
        <button id="tab-noncoffee" onclick="showCategory('noncoffee')" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🍫 Non-Coffee</button>
        <button id="tab-juice" onclick="showCategory('juice')" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-all bg-cream-100 text-stamp-600">🥭 Fruit Juice</button>
      </div>
      <p class="text-[11px] text-stamp-300 font-semibold mb-3">🧊 Iced &amp; other drinks come in Small (₱35) / Medium (₱40) / Large (₱50) — 🔥 Hot Coffee is one size, ₱35.</p>

      <div id="cat-hot" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <button onclick="addToCart('hot-americano','Hot Americano',35)" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/americano.jpg') }}" alt="Hot Americano" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Hot Americano</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35.00</p>
        </button>
        <button onclick="addToCart('hot-cafe-latte','Hot Cafe Latte',35)" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/cafe-latte.jpg') }}" alt="Hot Cafe Latte" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Hot Cafe Latte</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35.00</p>
        </button>
        <button onclick="addToCart('hot-cappuccino','Hot Cappuccino',35)" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/cappuccino.jpg') }}" alt="Hot Cappuccino" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Hot Cappuccino</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35.00</p>
        </button>
        <button onclick="addToCart('hot-spanish-latte','Hot Spanish Latte',35)" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/spanish-latte.jpg') }}" alt="Hot Spanish Latte" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Hot Spanish Latte</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35.00</p>
        </button>
        <button onclick="addToCart('hot-caramel-macchiato','Hot Caramel Macchiato',35)" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/caramel-macchiato.jpg') }}" alt="Hot Caramel Macchiato" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Hot Caramel Macchiato</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35.00</p>
        </button>
      </div>

      <div id="cat-coffee" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <button onclick="openSizePicker('iced-americano','Iced Americano','{{ asset('images/products/americano.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/americano.jpg') }}" alt="Iced Americano" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Iced Americano</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('iced-cafe-latte','Iced Cafe Latte','{{ asset('images/products/cafe-latte.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/cafe-latte.jpg') }}" alt="Iced Cafe Latte" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Iced Cafe Latte</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('iced-cappuccino','Iced Cappuccino','{{ asset('images/products/cappuccino.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/cappuccino.jpg') }}" alt="Iced Cappuccino" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Iced Cappuccino</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('iced-spanish-latte','Iced Spanish Latte','{{ asset('images/products/spanish-latte.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/spanish-latte.jpg') }}" alt="Iced Spanish Latte" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Iced Spanish Latte</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('iced-caramel-macchiato','Iced Caramel Macchiato','{{ asset('images/products/caramel-macchiato.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/caramel-macchiato.jpg') }}" alt="Iced Caramel Macchiato" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Iced Caramel Macchiato</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
      </div>

      <div id="cat-noncoffee" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <button onclick="openSizePicker('matcha-latte','Matcha Latte','{{ asset('images/products/matcha-latte.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/matcha-latte.jpg') }}" alt="Matcha Latte" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Matcha Latte</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('choco','Chocolate','{{ asset('images/products/chocolate.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/chocolate.jpg') }}" alt="Chocolate" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Chocolate</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('strawberry-milk','Strawberry Milk','{{ asset('images/products/strawberry-milk.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/strawberry-milk.jpg') }}" alt="Strawberry Milk" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Strawberry Milk</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
      </div>

      <div id="cat-juice" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
        <button onclick="openSizePicker('mango-juice','Mango Juice','{{ asset('images/products/mango-juice.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/mango-juice.jpg') }}" alt="Mango Juice" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Mango Juice</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('lemonade','Lemonade','{{ asset('images/products/lemonade.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/lemonade.jpg') }}" alt="Lemonade" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Lemonade</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
        <button onclick="openSizePicker('blue-lemonade','Blue Lemonade','{{ asset('images/products/blue-lemonade.jpg') }}')" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left hover:-translate-y-1 transition-transform">
          <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
            <img src="{{ asset('images/products/blue-lemonade.jpg') }}" alt="Blue Lemonade" class="w-full h-full object-cover">
            <span class="add-badge absolute bottom-2 right-2 w-8 h-8 rounded-full bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn text-cream-50 flex items-center justify-center font-bold text-lg group-hover:scale-110 active:scale-90 transition-transform">+</span>
          </div>
          <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate">Blue Lemonade</p>
          <p class="font-display font-bold text-stamp-500 text-sm">₱35–50</p>
        </button>
      </div>
    </div>

    <!-- Cart -->
    <div id="cartDrawer" class="w-full lg:w-96 shrink-0 lg:fixed lg:top-20 lg:bottom-4 lg:right-4 lg:z-30 lg:transition-transform lg:duration-300 lg:translate-x-full relative">
      <button id="cartTabBtn" onclick="toggleCartDrawer()" class="hidden lg:flex absolute top-1/2 -left-11 -translate-y-1/2 flex-col items-center gap-2 bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 rounded-l-2xl shadow-soft-btn px-2.5 py-4 z-10">
        <svg id="cartTabChevron" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <span class="[writing-mode:vertical-rl] rotate-180 font-display font-bold text-xs tracking-widest">CURRENT ORDER</span>
        <span id="cartTabBadge" class="w-5 h-5 rounded-full bg-coral-500 text-[10px] font-extrabold flex items-center justify-center">0</span>
      </button>
      <div id="cartPanel" class="bg-white rounded-[2rem] shadow-soft p-5 lg:sticky lg:top-0 lg:h-full lg:overflow-y-auto transition-shadow duration-300">
        <h2 class="font-display font-bold text-lg text-stamp-700 mb-3">Current Order</h2>

        <div id="cartList" class="space-y-3 max-h-64 overflow-y-auto mb-4 pr-1">
          <p class="text-center text-stamp-300 text-sm py-10">No items yet — tap a product to add it.</p>
        </div>

        <div class="flex items-center gap-2 mb-4">
          <div class="flex-1 bg-cream-100 rounded-xl shadow-soft-inset px-3 py-2">
            <input id="promoInput" type="text" placeholder="Promo code (try CATLOVE10)" class="w-full bg-transparent outline-none text-sm text-stamp-700 placeholder-stamp-300 font-semibold">
          </div>
          <button onclick="applyPromo()" class="px-4 py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors">Apply</button>
        </div>
        <p id="promoBanner" class="hidden text-xs font-bold text-mint-600 bg-mint-50 rounded-lg px-3 py-1.5 mb-4">Promo applied</p>

        <div class="space-y-1.5 mb-4 text-sm font-semibold text-stamp-500">
          <div class="flex justify-between"><span>Subtotal</span><span id="subtotalText">₱0.00</span></div>
          <div id="discountRow" class="hidden flex justify-between text-mint-600"><span>Discount</span><span id="discountText">-₱0.00</span></div>
          <div class="flex justify-between font-display font-bold text-stamp-700 text-lg pt-1 border-t border-cream-200"><span>Total</span><span id="totalText">₱0.00</span></div>
        </div>

        <p class="text-xs font-extrabold uppercase tracking-wide text-stamp-300 mb-2">Payment Method</p>
        <div class="grid grid-cols-3 gap-2 mb-4">
          <button onclick="selectPayment('Cash', this)" class="pay-method py-2.5 rounded-xl text-xs font-extrabold transition-all bg-stamp-500 text-cream-50 shadow-soft-btn">Cash</button>
          <button onclick="selectPayment('GCash', this)" class="pay-method py-2.5 rounded-xl text-xs font-extrabold transition-all bg-cream-100 text-stamp-600">GCash</button>
          <button onclick="selectPayment('Card', this)" class="pay-method py-2.5 rounded-xl text-xs font-extrabold transition-all bg-cream-100 text-stamp-600">Card</button>
        </div>

        <div id="cashSection" class="mb-5">
          <p class="text-xs font-extrabold uppercase tracking-wide text-stamp-300 mb-2">Cash Received</p>
          <div class="flex items-center gap-2 bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3 mb-2">
            <span class="font-display font-bold text-stamp-500">₱</span>
            <input id="cashInput" type="text" inputmode="decimal" readonly placeholder="0.00" oninput="updateChange()" class="w-full bg-transparent outline-none text-stamp-700 font-display font-bold text-lg">
          </div>
          <div class="lg:hidden grid grid-cols-3 gap-2 mb-3">
            <button onclick="setCashReceived('exact')" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">Exact</button>
            <button onclick="setCashReceived(100)" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">₱100</button>
            <button onclick="setCashReceived(500)" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">₱500</button>
          </div>

          <!-- Cash numeric keypad: touch devices only, hidden on laptop/desktop where typing works -->
          <div id="cashKeypad" class="lg:hidden grid grid-cols-3 gap-2 mb-3">
            <button onclick="keypadPress('1')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">1</button>
            <button onclick="keypadPress('2')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">2</button>
            <button onclick="keypadPress('3')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">3</button>
            <button onclick="keypadPress('4')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">4</button>
            <button onclick="keypadPress('5')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">5</button>
            <button onclick="keypadPress('6')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">6</button>
            <button onclick="keypadPress('7')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">7</button>
            <button onclick="keypadPress('8')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">8</button>
            <button onclick="keypadPress('9')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">9</button>
            <button onclick="keypadPress('C')" class="aspect-square rounded-2xl bg-coral-50 shadow-soft-sm font-display font-bold text-coral-500 text-sm sm:text-base transition-all active:shadow-none active:translate-y-[2px]">C</button>
            <button onclick="keypadPress('0')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg sm:text-xl transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100">0</button>
            <button onclick="keypadPress('back')" class="aspect-square rounded-2xl bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 transition-all active:shadow-none active:translate-y-[2px] active:bg-stamp-100 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l-7-7 7-7m-7 7h18"/></svg>
            </button>
          </div>

          <div class="flex justify-between items-center bg-cream-50 rounded-xl px-3 py-2.5">
            <span class="text-xs font-extrabold uppercase tracking-wide text-stamp-300">Change</span>
            <span id="changeText" class="font-display font-bold text-lg text-stamp-700 transition-colors">₱0.00</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button onclick="voidOrder()" class="admin-only px-4 py-3.5 rounded-2xl bg-coral-50 text-coral-600 font-display font-bold text-sm hover:bg-coral-100 transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Void
          </button>
          <button id="chargeBtn" onclick="charge()" disabled class="flex-1 py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 disabled:opacity-40 disabled:pointer-events-none">
            Charge <span id="chargeAmount">₱0.00</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating cart bar: mobile/tablet only, stays stuck to the bottom while scrolling -->
  <button id="floatingCartBar" onclick="scrollToCart()" class="lg:hidden fixed bottom-4 left-4 right-4 z-40 bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 rounded-full shadow-soft-btn px-5 py-3.5 flex items-center justify-between gap-3 transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2.5">
      <span class="relative shrink-0">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h1.5l.9 4.5M6.4 7.5h13.85a.6.6 0 01.58.76l-1.62 6a.6.6 0 01-.58.44H8.1M6.4 7.5L8.1 14.7M8.1 14.7L7 17.4a.6.6 0 00.6.8h10.9M10 20.5a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
        <span id="floatingCartCount" class="absolute -top-2 -right-2 bg-coral-500 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center">0</span>
      </span>
      <span class="font-display font-bold text-sm">View Order</span>
    </div>
    <div class="flex items-center gap-2">
      <span id="floatingCartTotal" class="font-display font-bold">₱0.00</span>
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
    </div>
  </button>

  <!-- Receipt Modal -->
  <div id="receiptModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-sm p-7 relative max-h-[90vh] overflow-y-auto">
      <div class="flex flex-col items-center mb-4">
        <div id="receiptCheck" class="w-12 h-12 rounded-full bg-mint-50 flex items-center justify-center text-mint-600 mb-2">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="font-display font-bold text-lg text-stamp-700">Payment Successful</h3>
      </div>
      <div id="receiptBody" class="text-sm"></div>
      <div class="flex gap-3 mt-5">
        <button onclick="window.print()" class="flex-1 py-3 rounded-2xl bg-cream-100 text-stamp-600 font-display font-bold text-sm hover:bg-cream-200 transition-colors">Print</button>
        <button onclick="closeReceipt()" class="flex-1 py-3 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn">New Sale</button>
      </div>
    </div>
  </div>

  <!-- Size Picker Modal -->
  <div id="sizeModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50" onclick="if(event.target===this) closeSizePicker()">
    <div id="sizeModalCard" class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-xs p-6 relative opacity-0 scale-90 transition-all duration-200">
      <button onclick="closeSizePicker()" class="absolute top-4 right-4 text-stamp-300 hover:text-stamp-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <img id="sizeModalImg" src="" alt="" class="w-24 h-24 rounded-2xl object-cover mx-auto shadow-soft-inset ring-1 ring-cream-200 mb-3">
      <p id="sizeModalName" class="text-center font-display font-bold text-stamp-700 text-lg mb-1">Product</p>
      <p class="text-center text-[11px] text-stamp-300 font-bold uppercase tracking-wide mb-4">Choose a cup size</p>
      <div class="grid grid-cols-3 gap-2.5">
        <button onclick="chooseSize('S', this)" class="size-btn bg-cream-100 rounded-2xl py-3 shadow-soft-sm hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition-all text-center">
          <span class="block text-2xl mb-1">🥤</span>
          <span class="block font-display font-bold text-stamp-700 text-sm">Small</span>
          <span class="block font-bold text-stamp-500 text-xs">₱35</span>
        </button>
        <button onclick="chooseSize('M', this)" class="size-btn bg-cream-100 rounded-2xl py-3 shadow-soft-sm hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition-all text-center">
          <span class="block text-3xl mb-1">🥤</span>
          <span class="block font-display font-bold text-stamp-700 text-sm">Medium</span>
          <span class="block font-bold text-stamp-500 text-xs">₱40</span>
        </button>
        <button onclick="chooseSize('L', this)" class="size-btn bg-cream-100 rounded-2xl py-3 shadow-soft-sm hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition-all text-center">
          <span class="block text-4xl mb-1">🥤</span>
          <span class="block font-display font-bold text-stamp-700 text-sm">Large</span>
          <span class="block font-bold text-stamp-500 text-xs">₱50</span>
        </button>
      </div>
    </div>
  </div>

  <script>
    let cart = [];
    let promoDiscount = 0;
    let selectedPayment = 'Cash';
    let currentOrderType = 'Dine In';
    let currentRole = 'owner';
    let currentUser = { name: 'Admin', role: 'Owner' };

    function showCategory(cat){
      ['hot','coffee','noncoffee','juice'].forEach(c => {
        document.getElementById('cat-' + c).classList.toggle('hidden', c !== cat);
        const tab = document.getElementById('tab-' + c);
        tab.classList.toggle('bg-stamp-500', c === cat);
        tab.classList.toggle('text-cream-50', c === cat);
        tab.classList.toggle('shadow-soft-btn', c === cat);
        tab.classList.toggle('bg-cream-100', c !== cat);
        tab.classList.toggle('text-stamp-600', c !== cat);
      });
      animateCategory(cat);
    }

    function animateCategory(cat){
      document.querySelectorAll('#cat-' + cat + ' > button').forEach((btn, i) => {
        btn.classList.remove('animate-fade-up');
        void btn.offsetWidth;
        btn.style.animationDelay = (i * 40) + 'ms';
        btn.classList.add('animate-fade-up');
      });
    }

    const SIZE_LABELS = { S: 'Small', M: 'Medium', L: 'Large' };
    const SIZE_PRICES = { S: 35, M: 40, L: 50 };
    let pendingProduct = null;

    function openSizePicker(id, name, img){
      pendingProduct = { id, name, img };
      document.getElementById('sizeModalImg').src = img;
      document.getElementById('sizeModalImg').alt = name;
      document.getElementById('sizeModalName').textContent = name;
      const modal = document.getElementById('sizeModal');
      const card = document.getElementById('sizeModalCard');
      modal.classList.remove('hidden');
      requestAnimationFrame(() => {
        card.classList.remove('opacity-0', 'scale-90');
        card.classList.add('opacity-100', 'scale-100');
      });
    }
    function closeSizePicker(){
      const modal = document.getElementById('sizeModal');
      const card = document.getElementById('sizeModalCard');
      card.classList.remove('opacity-100', 'scale-100');
      card.classList.add('opacity-0', 'scale-90');
      setTimeout(() => modal.classList.add('hidden'), 150);
    }
    function chooseSize(size, el){
      if (!pendingProduct) return;
      el.classList.add('animate-bounce-in');
      const label = SIZE_LABELS[size];
      const price = SIZE_PRICES[size];
      setTimeout(() => {
        addToCart(pendingProduct.id + '-' + size, pendingProduct.name + ' (' + label + ')', price);
        closeSizePicker();
      }, 120);
    }

    function setOrderType(type){
      currentOrderType = type;
      document.getElementById('typeDineIn').classList.toggle('bg-stamp-500', type === 'Dine In');
      document.getElementById('typeDineIn').classList.toggle('text-cream-50', type === 'Dine In');
      document.getElementById('typeDineIn').classList.toggle('text-stamp-500', type !== 'Dine In');
      document.getElementById('typeTakeOut').classList.toggle('bg-stamp-500', type === 'Take Out');
      document.getElementById('typeTakeOut').classList.toggle('text-cream-50', type === 'Take Out');
      document.getElementById('typeTakeOut').classList.toggle('text-stamp-500', type !== 'Take Out');
    }

    function format(n){ return '₱' + n.toFixed(2); }

    function addToCart(id, name, price){
      const existing = cart.find(i => i.id === id);
      if (existing) { existing.qty++; } else { cart.push({ id, name, price, qty: 1 }); }
      renderCart();
      pulseCart();
    }

    function pulseCart(){
      const panel = document.getElementById('cartPanel');
      panel.classList.remove('shadow-soft');
      panel.classList.add('ring-2', 'ring-mint-500', 'shadow-soft');
      setTimeout(() => panel.classList.remove('ring-2', 'ring-mint-500'), 350);
    }
    function changeQty(id, delta){
      const item = cart.find(i => i.id === id);
      if (!item) return;
      item.qty += delta;
      if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
      renderCart();
    }
    function renderCart(){
      const list = document.getElementById('cartList');
      if (cart.length === 0) {
        list.innerHTML = '<p class="text-center text-stamp-300 text-sm py-10">No items yet — tap a product to add it.</p>';
      } else {
        list.innerHTML = cart.map((i, idx) => `
          <div class="flex items-center gap-3 animate-slide-in" style="animation-delay:${idx * 30}ms">
            <div class="flex-1 min-w-0">
              <p class="font-bold text-stamp-700 text-sm truncate">${i.name}</p>
              <p class="text-xs text-stamp-300 font-semibold">${format(i.price)} each</p>
            </div>
            <div class="flex items-center gap-2 bg-cream-100 rounded-full px-1 py-1">
              <button onclick="changeQty('${i.id}', -1)" class="w-6 h-6 rounded-full bg-white shadow-soft-sm text-stamp-600 font-bold text-sm flex items-center justify-center active:scale-90 transition-transform">−</button>
              <span class="w-5 text-center text-sm font-extrabold text-stamp-700">${i.qty}</span>
              <button onclick="changeQty('${i.id}', 1)" class="w-6 h-6 rounded-full bg-white shadow-soft-sm text-stamp-600 font-bold text-sm flex items-center justify-center active:scale-90 transition-transform">+</button>
            </div>
            <p class="font-display font-bold text-stamp-700 text-sm w-16 text-right">${format(i.price * i.qty)}</p>
          </div>
        `).join('');
      }
      updateTotals();
      updateFloatingCart();
    }

    function updateFloatingCart(){
      const bar = document.getElementById('floatingCartBar');
      const count = cart.reduce((s, i) => s + i.qty, 0);
      const { total } = computeTotals();
      document.getElementById('floatingCartCount').textContent = count;
      document.getElementById('floatingCartTotal').textContent = format(total);
      document.getElementById('cartTabBadge').textContent = count;
      const shown = ['translate-y-0', 'opacity-100', 'pointer-events-auto'];
      const hidden = ['translate-y-24', 'opacity-0', 'pointer-events-none'];
      if (count > 0) {
        bar.classList.remove(...hidden);
        bar.classList.add(...shown);
        if (!cartDrawerOpen && window.matchMedia('(min-width: 1024px)').matches) {
          setCartDrawerOpen(true);
        }
      } else {
        bar.classList.remove(...shown);
        bar.classList.add(...hidden);
      }
    }

    function scrollToCart(){
      document.getElementById('cartPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
      setTimeout(pulseCart, 450);
    }

    let cartDrawerOpen = false;
    function setCartDrawerOpen(open){
      cartDrawerOpen = open;
      document.getElementById('cartDrawer').classList.toggle('lg:translate-x-0', open);
      document.getElementById('cartDrawer').classList.toggle('lg:translate-x-full', !open);
      document.getElementById('cartTabChevron').classList.toggle('rotate-180', open);
    }
    function toggleCartDrawer(){ setCartDrawerOpen(!cartDrawerOpen); }

    function applyPromo(){
      const code = document.getElementById('promoInput').value.trim().toUpperCase();
      const banner = document.getElementById('promoBanner');
      if (code === 'CATLOVE10') {
        promoDiscount = 0.10;
        banner.textContent = 'CATLOVE10 applied — 10% off';
        banner.classList.remove('hidden');
      } else {
        promoDiscount = 0;
        banner.classList.add('hidden');
        if (code) alert('Invalid or expired promo code.');
      }
      updateTotals();
    }

    function computeTotals(){
      const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
      const discount = subtotal * promoDiscount;
      const total = subtotal - discount;
      return { subtotal, discount, total };
    }

    function updateTotals(){
      const { subtotal, discount, total } = computeTotals();
      document.getElementById('subtotalText').textContent = format(subtotal);
      document.getElementById('discountRow').classList.toggle('hidden', discount <= 0);
      document.getElementById('discountText').textContent = '-' + format(discount);
      document.getElementById('totalText').textContent = format(total);
      document.getElementById('chargeAmount').textContent = format(total);
      updateChange();
    }

    function updateChange(){
      const { total } = computeTotals();
      const cash = parseFloat(document.getElementById('cashInput').value) || 0;
      const change = cash - total;
      const changeEl = document.getElementById('changeText');
      changeEl.textContent = (change < 0 ? '-' : '') + format(Math.abs(change));
      changeEl.classList.toggle('text-coral-500', change < 0);
      changeEl.classList.toggle('text-stamp-700', change >= 0);
      updateChargeState();
    }

    function setCashReceived(amount){
      const { total } = computeTotals();
      document.getElementById('cashInput').value = (amount === 'exact' ? total : amount).toFixed(2);
      updateChange();
    }

    function syncCashInputMode(){
      const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
      const input = document.getElementById('cashInput');
      input.readOnly = !isDesktop;
      input.classList.toggle('cursor-default', !isDesktop);
    }
    window.addEventListener('resize', syncCashInputMode);

    function keypadPress(key){
      const input = document.getElementById('cashInput');
      let val = input.value;
      if (key === 'C') {
        val = '';
      } else if (key === 'back') {
        val = val.slice(0, -1);
      } else if (key === '.') {
        if (!val.includes('.')) val += '.';
      } else {
        if (val.replace('.', '').length < 6) val += key;
      }
      input.value = val;
      updateChange();
    }

    function updateChargeState(){
      const { total } = computeTotals();
      const cash = parseFloat(document.getElementById('cashInput').value) || 0;
      const cashOk = selectedPayment !== 'Cash' || cash >= total;
      document.getElementById('chargeBtn').disabled = cart.length === 0 || !cashOk;
    }

    function selectPayment(method, el){
      selectedPayment = method;
      document.querySelectorAll('.pay-method').forEach(b => {
        b.classList.remove('bg-stamp-500', 'text-cream-50', 'shadow-soft-btn');
        b.classList.add('bg-cream-100', 'text-stamp-600');
      });
      el.classList.remove('bg-cream-100', 'text-stamp-600');
      el.classList.add('bg-stamp-500', 'text-cream-50', 'shadow-soft-btn');
      document.getElementById('cashSection').classList.toggle('hidden', method !== 'Cash');
      updateChargeState();
    }

    function voidOrder(){
      if (cart.length === 0) return;
      if (confirm('Void this order? This clears the current sale.')) {
        cart = [];
        promoDiscount = 0;
        document.getElementById('promoInput').value = '';
        document.getElementById('promoBanner').classList.add('hidden');
        document.getElementById('cashInput').value = '';
        renderCart();
      }
    }

    function charge(){
      if (cart.length === 0) return;
      const { subtotal, discount, total } = computeTotals();

      let cashReceived = null, change = null;
      if (selectedPayment === 'Cash') {
        cashReceived = parseFloat(document.getElementById('cashInput').value) || 0;
        if (cashReceived < total) return;
        change = cashReceived - total;
      }

      let counter = parseInt(localStorage.getItem('cb_txn_counter') || '481', 10) + 1;
      localStorage.setItem('cb_txn_counter', String(counter));

      const now = new Date();
      const stamp = now.toISOString().slice(0, 10).replace(/-/g, '');

      const txn = {
        id: 'CB-' + stamp + '-' + String(counter).padStart(4, '0'),
        datetime: now.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' }),
        items: cart.map(i => ({ name: i.name, qty: i.qty, price: i.price })),
        subtotal, discount, total,
        payment: selectedPayment,
        cashReceived, change,
        orderType: currentOrderType,
        processedBy: currentUser.name,
        processedByRole: currentUser.role,
      };

      const txns = JSON.parse(localStorage.getItem('cb_transactions') || '[]');
      txns.unshift(txn);
      localStorage.setItem('cb_transactions', JSON.stringify(txns));

      showReceipt(txn);
      cart = [];
      promoDiscount = 0;
      document.getElementById('promoInput').value = '';
      document.getElementById('promoBanner').classList.add('hidden');
      document.getElementById('cashInput').value = '';
      renderCart();
    }

    function showReceipt(txn){
      const check = document.getElementById('receiptCheck');
      check.classList.remove('animate-bounce-in');
      void check.offsetWidth;
      check.classList.add('animate-bounce-in');
      document.getElementById('receiptBody').innerHTML = `
        <div class="border-t border-b border-dashed border-cream-200 py-3 mb-3 space-y-1">
          ${txn.items.map(i => `
            <div class="flex justify-between text-stamp-600 font-semibold">
              <span>${i.qty}x ${i.name}</span>
              <span>${format(i.price * i.qty)}</span>
            </div>
          `).join('')}
        </div>
        <div class="space-y-1 font-semibold text-stamp-500 mb-3">
          <div class="flex justify-between"><span>Subtotal</span><span>${format(txn.subtotal)}</span></div>
          ${txn.discount > 0 ? `<div class="flex justify-between text-mint-600"><span>Discount</span><span>-${format(txn.discount)}</span></div>` : ''}
          <div class="flex justify-between font-display font-bold text-stamp-700 text-base pt-1 border-t border-cream-200"><span>Total</span><span>${format(txn.total)}</span></div>
        </div>
        <div class="space-y-1 text-xs text-stamp-400 font-semibold">
          <div class="flex justify-between"><span>Transaction ID</span><span class="font-bold text-stamp-600">${txn.id}</span></div>
          <div class="flex justify-between"><span>Date/Time</span><span>${txn.datetime}</span></div>
          <div class="flex justify-between"><span>Order Type</span><span>${txn.orderType}</span></div>
          <div class="flex justify-between"><span>Payment Method</span><span>${txn.payment}</span></div>
          ${txn.payment === 'Cash' ? `
            <div class="flex justify-between"><span>Cash Received</span><span>${format(txn.cashReceived)}</span></div>
            <div class="flex justify-between font-bold text-mint-600"><span>Change</span><span>${format(txn.change)}</span></div>
          ` : ''}
          <div class="flex justify-between"><span>Processed By</span><span>${txn.processedBy} · ${txn.processedByRole}</span></div>
        </div>
        <p class="text-center text-[11px] text-stamp-300 font-bold mt-4">Thank you, meow~! 🐾</p>
      `;
      document.getElementById('receiptModal').classList.remove('hidden');
    }
    function closeReceipt(){ document.getElementById('receiptModal').classList.add('hidden'); }

    function applyRole(role){
      currentRole = role;
      currentUser = role === 'owner' ? { name: 'Admin', role: 'Owner' } : { name: 'Juan Dela Cruz', role: 'Staff' };
      document.getElementById('currentUserName').textContent = currentUser.name;
      document.getElementById('currentUserRole').textContent = currentUser.role;
      document.getElementById('userAvatar').textContent = role === 'owner' ? 'A' : 'JD';
      document.getElementById('userAvatar').classList.toggle('bg-stamp-500', role === 'owner');
      document.getElementById('userAvatar').classList.toggle('bg-mint-500', role !== 'owner');

      document.querySelectorAll('.admin-only').forEach(el => el.classList.toggle('hidden', role !== 'owner'));

      document.getElementById('roleOwnerBtn').classList.toggle('bg-stamp-500', role === 'owner');
      document.getElementById('roleOwnerBtn').classList.toggle('text-cream-50', role === 'owner');
      document.getElementById('roleOwnerBtn').classList.toggle('text-stamp-500', role !== 'owner');
      document.getElementById('roleStaffBtn').classList.toggle('bg-mint-500', role === 'staff');
      document.getElementById('roleStaffBtn').classList.toggle('text-cream-50', role === 'staff');
      document.getElementById('roleStaffBtn').classList.toggle('text-stamp-500', role !== 'staff');
    }

    const params = new URLSearchParams(location.search);
    applyRole(params.get('role') === 'staff' ? 'staff' : 'owner');
    renderCart();
    animateCategory('hot');
    syncCashInputMode();
  </script>

</body>
</html>

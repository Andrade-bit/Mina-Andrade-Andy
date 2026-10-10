<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews POS Terminal</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
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
<style>
  @media (max-width: 1023px) {
    #cartDrawer {
      position: fixed; left: 0; right: 0; bottom: 0; z-index: 50;
      width: auto; max-width: 560px; margin: 0 auto; padding: 0 10px 10px;
      transform: translateY(110%); transition: transform .32s cubic-bezier(.22,1,.36,1);
      pointer-events: none;
    }
    #cartDrawer.sheet-open { transform: translateY(0); pointer-events: auto; }
    #cartDrawer.sheet-dragging { transition: none; }
    #cartPanel {
      max-height: calc(100dvh - 90px); overflow-y: auto; overscroll-behavior: contain;
      border-radius: 1.75rem; box-shadow: 0 -12px 40px rgba(26,59,82,.28);
    }
    #cartBackdrop.show { display: block; animation: fadeIn .25s ease-out both; }
  }
  @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
  /* keep the floating FAQ button clear of the order bar (phones) and the open order panel (desktop) */
  @media (max-width: 1023px) { #faqFab { bottom: calc(5.5rem + env(safe-area-inset-bottom, 0px)); } }
  @media (min-width: 1024px) { #faqFab.faq-shifted { right: 26rem; } }
</style>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700">

  <!-- Top bar -->
  <header class="bg-white shadow-soft-sm px-5 py-3 flex items-center justify-between flex-wrap gap-3 sticky top-0 z-30">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-cream-100 shadow-soft-inset flex items-center justify-center text-stamp-600 shrink-0">
        <img src="{{ asset('images/catbrews-logo.png') }}" alt="The Purrfect Cup — Catbrews logo" class="w-full h-full object-contain rounded-full" style="width:100%;height:100%;object-fit:contain">
      </div>
      <div>
        <p class="font-display font-bold text-stamp-700 leading-tight">Catbrews POS</p>
        <p class="text-[11px] text-stamp-300 font-bold">New Sale</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      @if ($credential->role === 'admin')
        <a href="{{ route('pos.transactions', ['back' => 'terminal']) }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Transactions &amp; reports">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Admin dashboard">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </a>
      @endif

      <div class="flex items-center gap-2 pl-2 border-l border-cream-200">
        <div class="w-9 h-9 rounded-full {{ $credential->role === 'admin' ? 'bg-stamp-500' : 'bg-mint-500' }} text-cream-50 flex items-center justify-center font-display font-bold text-xs">{{ strtoupper(substr($credential->first_name, 0, 1).substr($credential->last_name, 0, 1)) }}</div>
        <div class="hidden md:block">
          <p class="text-sm font-extrabold text-stamp-700 leading-tight">{{ $credential->first_name }} {{ $credential->last_name }}</p>
          <p class="text-[10px] font-bold text-stamp-300 uppercase tracking-wide">{{ ucfirst($credential->role) }}</p>
        </div>
      </div>
      <form method="POST" action="{{ route('pos.logout') }}">
        @csrf
        <button type="submit" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors" title="Switch user">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
      </form>
    </div>
  </header>

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

      @php
        $categoryTabs = ['Hot Coffee' => 'hot', 'Iced Coffee' => 'coffee', 'Non-Coffee' => 'noncoffee', 'Fruit Juice' => 'juice'];
      @endphp

      @foreach ($categoryTabs as $categoryName => $tabId)
        <div id="cat-{{ $tabId }}" class="{{ $loop->first ? '' : 'hidden' }} grid grid-cols-2 sm:grid-cols-3 gap-4">
          @foreach ($products->where('productCategory.category_name', $categoryName) as $product)
            @php $sizes = $product->sizes; @endphp
            @if ($product->blocked_by->isNotEmpty())
              <div class="product-card relative bg-white rounded-3xl shadow-soft p-3 text-left cursor-not-allowed select-none" aria-disabled="true" title="Not available: out of {{ $product->blocked_by->join(', ', ' and ') }}">
                <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
                  <img src="{{ $product->display_image }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover grayscale opacity-60">
                  <span class="absolute top-2 left-2 bg-coral-500 text-cream-50 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full shadow-soft-sm">Not available</span>
                </div>
                <p class="font-extrabold text-stamp-400 text-sm mt-2.5 truncate">{{ $product->product_name }}</p>
                <p class="text-[11px] font-bold text-coral-600 truncate">Out of {{ $product->blocked_by->first() }}@if ($product->blocked_by->count() > 1) +{{ $product->blocked_by->count() - 1 }} more @endif</p>
              </div>
            @elseif ($sizes->count() === 1)
              <button onclick="addOneSizeToCart({{ $product->id }}, {{ $sizes->first()->cup_size_id }}, {{ Illuminate\Support\Js::from($product->product_name) }}, {{ $sizes->first()->price }})" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-[0_24px_44px_-14px_rgba(26,59,82,0.32)] active:translate-y-0 active:scale-[0.97]">
                <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
                  <img src="{{ $product->display_image }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
                </div>
                <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate transition-colors duration-300 group-hover:text-stamp-500">{{ $product->product_name }}</p>
                <p class="font-display font-bold text-stamp-500 text-sm">₱{{ number_format($sizes->first()->price, 2) }}</p>
              </button>
            @else
              <button onclick="openSizePicker({{ $product->id }}, {{ Illuminate\Support\Js::from($product->product_name) }}, {{ Illuminate\Support\Js::from($product->display_image) }}, {{ Illuminate\Support\Js::from($sizes->values()) }})" class="product-card group bg-white rounded-3xl shadow-soft p-3 text-left transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-[0_24px_44px_-14px_rgba(26,59,82,0.32)] active:translate-y-0 active:scale-[0.97]">
                <div class="relative rounded-2xl overflow-hidden aspect-square shadow-soft-inset ring-1 ring-cream-200">
                  <img src="{{ $product->display_image }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
                </div>
                <p class="font-extrabold text-stamp-700 text-sm mt-2.5 truncate transition-colors duration-300 group-hover:text-stamp-500">{{ $product->product_name }}</p>
                <p class="font-display font-bold text-stamp-500 text-sm">₱{{ number_format($sizes->first()->price, 0) }}&ndash;{{ number_format($sizes->last()->price, 0) }}</p>
              </button>
            @endif
          @endforeach
        </div>
      @endforeach
    </div>

    <!-- Cart -->
    <div id="cartBackdrop" onclick="closeCartSheet()" class="hidden lg:hidden fixed inset-0 bg-stamp-700/40 backdrop-blur-sm z-40"></div>
    <div id="cartDrawer" class="w-full lg:w-96 shrink-0 lg:fixed lg:top-20 lg:bottom-4 lg:right-4 lg:z-30 lg:transition-transform lg:duration-300 lg:translate-x-full relative">
      <button id="cartTabBtn" onclick="toggleCartDrawer()" class="hidden lg:flex absolute top-1/2 right-full -mr-px -translate-y-1/2 flex-col items-center gap-2 bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 rounded-l-2xl shadow-soft-btn px-2.5 py-4 z-10">
        <svg id="cartTabChevron" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <span class="[writing-mode:vertical-rl] rotate-180 font-display font-bold text-xs tracking-widest">CURRENT ORDER</span>
        <span id="cartTabBadge" class="w-5 h-5 rounded-full bg-coral-500 text-[10px] font-extrabold flex items-center justify-center">0</span>
      </button>
      <div id="cartPanel" class="bg-white rounded-[2rem] shadow-soft p-5 lg:sticky lg:top-0 lg:h-full lg:overflow-y-auto transition-shadow duration-300">
        <div id="cartSheetHandle" class="lg:hidden -mt-1 mb-2 pt-1 touch-none cursor-grab">
          <div class="w-10 h-1.5 rounded-full bg-cream-200 mx-auto"></div>
        </div>
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-display font-bold text-lg text-stamp-700">Current Order</h2>
          <button type="button" onclick="closeCartSheet()" class="lg:hidden w-9 h-9 rounded-xl bg-cream-100 hover:bg-cream-200 flex items-center justify-center text-stamp-500" aria-label="Close order">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
        </div>

        @php
          $emptyOrderHtml = '<div class="flex items-center gap-3 rounded-2xl border border-cream-200 bg-cream-100 px-4 py-4 text-stamp-400" role="status">'
            .'<svg class="w-6 h-6 shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
            .'<div><p class="text-sm font-extrabold leading-tight">No order yet</p><p class="text-xs font-semibold opacity-80">Tap a product to start a new order.</p></div>'
            .'</div>';
        @endphp
        <div id="cartList" class="space-y-3 max-h-64 overflow-y-auto mb-4 pr-1">{!! $emptyOrderHtml !!}</div>

        <div class="flex items-center gap-2 mb-4">
          <div class="flex-1 bg-cream-100 rounded-xl shadow-soft-inset px-3 py-2">
            <label for="promoInput" class="sr-only">Promotion</label>
            <select id="promoInput" onchange="applyPromo()" class="w-full bg-transparent outline-none text-sm text-stamp-700 font-semibold">
              <option value="">No promotion</option>
              @foreach ($promos as $promo)
                <option value="{{ $promo->code }}">{{ $promo->code }} — {{ $promo->type === 'percent' ? rtrim(rtrim($promo->value, '0'), '.').'%' : '₱'.number_format($promo->value, 2) }} off</option>
              @endforeach
            </select>
          </div>
          <button type="button" onclick="applyPromo()" class="px-4 py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors">Apply</button>
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
            <input id="cashInput" type="text" inputmode="decimal" placeholder="0.00" oninput="updateChange()" class="w-full bg-transparent outline-none text-stamp-700 font-display font-bold text-lg">
          </div>
          <div class="lg:hidden grid grid-cols-3 gap-2 mb-3">
            <button onclick="setCashReceived('exact')" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">Exact</button>
            <button onclick="setCashReceived(100)" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">₱100</button>
            <button onclick="setCashReceived(500)" class="py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 font-extrabold text-xs transition-colors active:scale-90">₱500</button>
          </div>

          <div class="flex justify-between items-center bg-cream-50 rounded-xl px-3 py-2.5">
            <span class="text-xs font-extrabold uppercase tracking-wide text-stamp-300">Change</span>
            <span id="changeText" class="font-display font-bold text-lg text-stamp-700 transition-colors">₱0.00</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button onclick="clearOrder()" class="px-4 py-3.5 rounded-2xl bg-coral-50 text-coral-600 font-display font-bold text-sm hover:bg-coral-100 transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Clear
          </button>
          <button id="chargeBtn" onclick="charge()" disabled class="flex-1 py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 disabled:opacity-40 disabled:pointer-events-none">
            Charge <span id="chargeAmount">₱0.00</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating cart bar: mobile/tablet only, stays stuck to the bottom while scrolling -->
  <button id="floatingCartBar" onclick="openCartSheet()" class="lg:hidden fixed bottom-4 left-4 right-4 z-40 bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 rounded-full shadow-soft-btn px-5 py-3.5 flex items-center justify-between gap-3 transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
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
      <div id="sizeModalOptions" class="grid grid-cols-3 gap-2.5"></div>
    </div>
  </div>

  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let cart = [];
    const cartSyncUrl = '{{ route('pos.terminal.cart') }}';
    let cartSyncTimer = null;

    function syncCartToServer(){
      fetch(cartSyncUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_ids: [...new Set(cart.map(i => i.productId))] }),
        keepalive: true,
      }).catch(() => {});
    }
    function scheduleCartSync(){
      clearTimeout(cartSyncTimer);
      cartSyncTimer = setTimeout(syncCartToServer, 300);
    }
    setInterval(() => { if (cart.length) syncCartToServer(); }, 60000);
    window.addEventListener('pagehide', () => {
      const data = new FormData();
      data.append('_token', csrfToken);
      navigator.sendBeacon(cartSyncUrl, data);
    });
    let selectedPayment = 'Cash';

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

    let pendingProduct = null;

    function openSizePicker(productId, name, img, sizes){
      pendingProduct = { productId, name, img, sizes };
      document.getElementById('sizeModalImg').src = img;
      document.getElementById('sizeModalImg').alt = name;
      document.getElementById('sizeModalName').textContent = name;
      document.getElementById('sizeModalOptions').innerHTML = sizes.map((size, i) => size.available === false ? `
        <button type="button" disabled aria-disabled="true" title="Out of ${size.lacking.join(', ')}" class="size-btn bg-cream-100 rounded-2xl py-3 shadow-soft-sm text-center opacity-60 cursor-not-allowed">
          <span class="block mb-1 grayscale" style="font-size: ${20 + i * 8}px">🥤</span>
          <span class="block font-display font-bold text-stamp-400 text-sm">${size.size_name}</span>
          <span class="block font-extrabold text-coral-600 text-[10px] uppercase leading-tight">Not available</span>
        </button>
      ` : `
        <button type="button" onclick="chooseSize(${i}, this)" class="size-btn bg-cream-100 rounded-2xl py-3 shadow-soft-sm hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition-all text-center">
          <span class="block mb-1" style="font-size: ${20 + i * 8}px">🥤</span>
          <span class="block font-display font-bold text-stamp-700 text-sm">${size.size_name}</span>
          <span class="block font-bold text-stamp-500 text-xs">₱${Number(size.price).toFixed(0)}</span>
        </button>
      `).join('');
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
    function chooseSize(index, el){
      if (!pendingProduct) return;
      const size = pendingProduct.sizes[index];
      const product = pendingProduct;
      el.classList.add('animate-bounce-in');
      setTimeout(() => {
        addToCart(product.productId, size.cup_size_id, product.name + ' (' + size.size_name + ')', Number(size.price));
        closeSizePicker();
      }, 120);
    }

    function addOneSizeToCart(productId, cupSizeId, name, price){
      addToCart(productId, cupSizeId, name, Number(price));
    }

    function format(n){ return '₱' + Number(n).toFixed(2); }

    function addToCart(productId, cupSizeId, name, price){
      const lineId = productId + '-' + cupSizeId;
      const existing = cart.find(i => i.lineId === lineId);
      if (existing) { existing.qty++; } else { cart.push({ lineId, productId, cupSizeId, name, price, qty: 1 }); }
      renderCart();
      pulseCart();
    }

    function pulseCart(){
      const panel = document.getElementById('cartPanel');
      panel.classList.remove('shadow-soft');
      panel.classList.add('ring-2', 'ring-mint-500', 'shadow-soft');
      setTimeout(() => panel.classList.remove('ring-2', 'ring-mint-500'), 350);
    }
    const EMPTY_ORDER_HTML = @json($emptyOrderHtml);
    const MAX_LINE_QTY = 99;

    // Live preview while typing: update this line and the totals without re-rendering (which would drop focus).
    function typeQty(input, lineId){
      input.value = input.value.replace(/\D/g, '').slice(0, 2);
      const item = cart.find(i => i.lineId === lineId);
      const qty = parseInt(input.value, 10);
      if (!item || !qty) return;
      item.qty = qty;
      input.closest('[data-line]').querySelector('[data-line-total]').textContent = format(item.price * qty);
      updateTotals();
      updateFloatingCart();
      scheduleCartSync();
    }

    // Commit on blur / Enter: 0 removes the line, an empty box goes back to the current quantity.
    function setQty(lineId, raw){
      const item = cart.find(i => i.lineId === lineId);
      if (!item) return;
      let qty = parseInt(raw, 10);
      if (isNaN(qty)) { renderCart(); return; }
      if (qty > MAX_LINE_QTY) {
        qty = MAX_LINE_QTY;
        toast('Maximum ' + MAX_LINE_QTY + ' of one item per order.', 'info');
      }
      if (qty <= 0) { cart = cart.filter(i => i.lineId !== lineId); } else { item.qty = qty; }
      renderCart();
    }

    function changeQty(lineId, delta){
      const item = cart.find(i => i.lineId === lineId);
      if (!item) return;
      item.qty += delta;
      if (item.qty <= 0) cart = cart.filter(i => i.lineId !== lineId);
      renderCart();
    }
    function renderCart(){
      const list = document.getElementById('cartList');
      if (cart.length === 0) {
        list.innerHTML = EMPTY_ORDER_HTML;
      } else {
        list.innerHTML = cart.map((i, idx) => `
          <div class="flex items-center gap-3 animate-slide-in" data-line="${i.lineId}" style="animation-delay:${idx * 30}ms">
            <div class="flex-1 min-w-0">
              <p class="font-bold text-stamp-700 text-sm truncate">${i.name}</p>
              <p class="text-xs text-stamp-300 font-semibold">${format(i.price)} each</p>
            </div>
            <div class="flex items-center gap-2 bg-cream-100 rounded-full px-1 py-1">
              <button onclick="changeQty('${i.lineId}', -1)" class="w-6 h-6 rounded-full bg-white shadow-soft-sm text-stamp-600 font-bold text-sm flex items-center justify-center active:scale-90 transition-transform">−</button>
              <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="2" value="${i.qty}" aria-label="Quantity" onfocus="this.select()" oninput="typeQty(this, '${i.lineId}')" onchange="setQty('${i.lineId}', this.value)" onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.blur(); }" class="w-8 text-center text-sm font-extrabold text-stamp-700 bg-transparent rounded-md outline-none focus:bg-white focus:shadow-soft-inset">
              <button onclick="changeQty('${i.lineId}', 1)" class="w-6 h-6 rounded-full bg-white shadow-soft-sm text-stamp-600 font-bold text-sm flex items-center justify-center active:scale-90 transition-transform">+</button>
            </div>
            <p data-line-total class="font-display font-bold text-stamp-700 text-sm w-16 text-right">${format(i.price * i.qty)}</p>
          </div>
        `).join('');
      }
      updateTotals();
      updateFloatingCart();
      scheduleCartSync();
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
      if (count > 0 && cartSheetOpen) {
        bar.classList.remove(...shown);
        bar.classList.add(...hidden);
      } else if (count > 0) {
        bar.classList.remove(...hidden);
        bar.classList.add(...shown);
        if (!cartDrawerOpen && window.matchMedia('(min-width: 1024px)').matches) {
          setCartDrawerOpen(true);
        }
      } else {
        bar.classList.remove(...shown);
        bar.classList.add(...hidden);
        if (cartSheetOpen) closeCartSheet();
      }
    }

    let cartSheetOpen = false;

    function openCartSheet(){
      cartSheetOpen = true;
      document.getElementById('cartDrawer').classList.add('sheet-open');
      document.getElementById('cartBackdrop').classList.add('show');
      document.body.style.overflow = 'hidden';
      document.getElementById('floatingCartBar').classList.add('opacity-0', 'pointer-events-none');
    }
    function closeCartSheet(){
      if (!cartSheetOpen) return;
      cartSheetOpen = false;
      const drawer = document.getElementById('cartDrawer');
      drawer.classList.remove('sheet-open', 'sheet-dragging');
      drawer.style.transform = '';
      document.getElementById('cartBackdrop').classList.remove('show');
      document.body.style.overflow = '';
      updateFloatingCart();
    }
    (function enableSheetDrag(){
      const handle = document.getElementById('cartSheetHandle');
      const drawer = document.getElementById('cartDrawer');
      let startY = null;
      handle.addEventListener('touchstart', e => { startY = e.touches[0].clientY; drawer.classList.add('sheet-dragging'); }, { passive: true });
      handle.addEventListener('touchmove', e => {
        if (startY === null) return;
        const dy = Math.max(0, e.touches[0].clientY - startY);
        drawer.style.transform = 'translateY(' + dy + 'px)';
      }, { passive: true });
      handle.addEventListener('touchend', e => {
        if (startY === null) return;
        const dy = e.changedTouches[0].clientY - startY;
        startY = null;
        drawer.classList.remove('sheet-dragging');
        if (dy > 90) { closeCartSheet(); } else { drawer.style.transform = ''; }
      });
    })();

    let cartDrawerOpen = false;
    function setCartDrawerOpen(open){
      cartDrawerOpen = open;
      document.getElementById('cartDrawer').classList.toggle('lg:translate-x-0', open);
      document.getElementById('cartDrawer').classList.toggle('lg:translate-x-full', !open);
      document.getElementById('cartTabChevron').classList.toggle('rotate-180', open);
      document.getElementById('faqFab').classList.toggle('faq-shifted', open);
    }
    function toggleCartDrawer(){ setCartDrawerOpen(!cartDrawerOpen); }

    let appliedPromoCode = '';
    let appliedPromo = null;
    let promoPending = false;
    let promoRequest = 0;

    async function applyPromo(){
      const code = document.getElementById('promoInput').value;
      const requestId = ++promoRequest;
      appliedPromoCode = '';
      appliedPromo = null;
      promoPending = !!code;
      const banner = document.getElementById('promoBanner');
      banner.classList.toggle('hidden', !code);
      banner.textContent = code ? 'Checking promotion…' : '';
      updateTotals();
      if (!code) return;
      try {
        const query = new URLSearchParams({promo_code: code, subtotal: cart.reduce((sum, item) => sum + item.price * item.qty, 0)});
        const response = await fetch('{{ route('pos.promos.preview') }}?' + query, {headers: {Accept: 'application/json'}, cache: 'no-store'});
        const data = await response.json();
        if (requestId !== promoRequest) return;
        if (!response.ok) throw new Error(data.message || 'Could not validate this promotion.');
        appliedPromo = data;
        appliedPromoCode = data.code;
        banner.textContent = data.code + ' applied. Discount is included below.';
      } catch (error) {
        if (requestId !== promoRequest) return;
        banner.textContent = error.message || 'Could not validate this promotion. Try again or select No promotion.';
        toast(banner.textContent, 'error');
      } finally {
        if (requestId === promoRequest) {
          promoPending = false;
          updateTotals();
        }
      }
    }

    function computeTotals(){
      const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
      const valid = appliedPromo && (!appliedPromo.expires_at || Date.now() <= Date.parse(appliedPromo.expires_at));
      const discount = valid ? Math.round(Math.min(subtotal, appliedPromo.type === 'percent' ? subtotal * appliedPromo.value / 100 : appliedPromo.value) * 100) / 100 : 0;
      const total = Math.round((subtotal - discount) * 100) / 100;
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

    function updateChargeState(){
      const { total } = computeTotals();
      const cash = parseFloat(document.getElementById('cashInput').value) || 0;
      const cashOk = selectedPayment !== 'Cash' || cash >= total;
      document.getElementById('chargeBtn').disabled = cart.length === 0 || !cashOk || promoPending || (document.getElementById('promoInput').value !== appliedPromoCode) || (appliedPromo?.expires_at && Date.now() > Date.parse(appliedPromo.expires_at));
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

    function clearOrder(){
      if (cart.length === 0) return;
      if (confirm('Clear this order? All items in the current cart will be removed.')) {
        cart = [];
        appliedPromoCode = '';
        appliedPromo = null;
        promoPending = false;
        ++promoRequest;
        document.getElementById('promoInput').value = '';
        document.getElementById('promoBanner').classList.add('hidden');
        document.getElementById('cashInput').value = '';
        renderCart();
      }
    }

    function charge(){
      updateChargeState();
      if (cart.length === 0 || document.getElementById('chargeBtn').disabled) return;
      const { total } = computeTotals();

      let cashReceived = null, change = null;
      if (selectedPayment === 'Cash') {
        cashReceived = parseFloat(document.getElementById('cashInput').value) || 0;
        if (cashReceived < total) return;
        change = cashReceived - total;
      }

      const chargeBtn = document.getElementById('chargeBtn');
      chargeBtn.disabled = true;

      fetch('{{ route('pos.terminal.store') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          payment_method: selectedPayment,
          promo_code: appliedPromoCode || null,
          items: cart.map(i => ({
            product_id: i.productId,
            cup_size_id: i.cupSizeId,
            quantity: i.qty,
            price_at_order: i.price,
          })),
        }),
      })
        .then(async (res) => {
          const data = await res.json();
          if (!res.ok) {
            if (res.status === 401) {
              toast(data.message || 'Your session expired. Please log in again.', 'error');
              setTimeout(() => { window.location.href = '{{ route('pos.login') }}'; }, 1500);
              return;
            }
            toast(data.message || 'Could not process this sale. Please try again.', 'error');
            return;
          }

          closeCartSheet();
          showReceipt(data.transaction, { cashReceived, change });
          cart = [];
          appliedPromoCode = '';
          appliedPromo = null;
          promoPending = false;
          ++promoRequest;
          document.getElementById('promoInput').value = '';
          document.getElementById('promoBanner').classList.add('hidden');
          document.getElementById('cashInput').value = '';
          renderCart();
        })
        .catch(() => toast('Network error — the sale was not recorded. Please try again.', 'error'))
        .finally(() => { chargeBtn.disabled = false; updateChargeState(); });
    }

    function showReceipt(transaction, extra){
      const check = document.getElementById('receiptCheck');
      check.classList.remove('animate-bounce-in');
      void check.offsetWidth;
      check.classList.add('animate-bounce-in');

      const items = transaction.items || [];
      const dateStr = new Date(transaction.transaction_date).toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });

      document.getElementById('receiptBody').innerHTML = `
        <div class="border-t border-b border-dashed border-cream-200 py-3 mb-3 space-y-1">
          ${items.map(i => `
            <div class="flex justify-between text-stamp-600 font-semibold">
              <span>${i.quantity}x ${i.product.product_name} (${i.cup_size.size_name})</span>
              <span>${format(i.subtotal)}</span>
            </div>
          `).join('')}
        </div>
        <div class="space-y-1 font-semibold text-stamp-500 mb-3">
          ${transaction.discount_amount > 0 ? `<div class="flex justify-between text-mint-600"><span>Promo (${transaction.promo ? transaction.promo.code : ''})</span><span>-${format(transaction.discount_amount)}</span></div>` : ''}
          <div class="flex justify-between font-display font-bold text-stamp-700 text-base pt-1 border-t border-cream-200"><span>Total</span><span>${format(transaction.total_amount)}</span></div>
        </div>
        <div class="space-y-1 text-xs text-stamp-400 font-semibold">
          <div class="flex justify-between"><span>Transaction ID</span><span class="font-bold text-stamp-600">CB-${String(transaction.id).padStart(5, '0')}</span></div>
          <div class="flex justify-between"><span>Date/Time</span><span>${dateStr}</span></div>
          <div class="flex justify-between"><span>Payment Method</span><span>${transaction.payment_method}</span></div>
          ${transaction.payment_method === 'Cash' ? `
            <div class="flex justify-between"><span>Cash Received</span><span>${format(extra.cashReceived)}</span></div>
            <div class="flex justify-between font-bold text-mint-600"><span>Change</span><span>${format(extra.change)}</span></div>
          ` : ''}
          <div class="flex justify-between"><span>Processed By</span><span>{{ $credential->first_name }} {{ $credential->last_name }} &middot; {{ ucfirst($credential->role) }}</span></div>
        </div>
        <p class="text-center text-[11px] text-stamp-300 font-bold mt-4">Thank you, meow~! 🐾</p>
      `;
      document.getElementById('receiptModal').classList.remove('hidden');
    }
    function closeReceipt(){ document.getElementById('receiptModal').classList.add('hidden'); }

    renderCart();
    animateCategory('hot');
  </script>

  @include('admin.partials.pos-faq')
  @include('admin.partials.toasts')

</body>
</html>

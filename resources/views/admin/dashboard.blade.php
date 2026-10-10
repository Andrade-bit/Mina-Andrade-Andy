<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Dashboard</title>
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

  @include('admin.partials.sidebar', ['active' => 'dashboard'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    @php
      $hour = now('Asia/Manila')->hour;
      $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
      <div>
      <h1 class="font-display font-bold text-3xl md:text-4xl text-stamp-700">Dashboard</h1>
      <p class="font-bold text-stamp-600">{{ $greeting }}, {{ auth()->user()->name }} 🐾</p>
      <p class="text-stamp-500 text-sm font-semibold mt-1">Here's how Catbrews is doing · {{ $periodLabel }}.</p>
      </div>
      <form method="GET" action="{{ route('admin.dashboard') }}" aria-label="Filter dashboard dates" class="flex max-w-full flex-wrap items-center gap-2">
        <div class="flex max-w-full items-center gap-2">
          <label class="sr-only" for="dashboard-from">From date</label>
          <input id="dashboard-from" type="date" name="from" value="{{ $from }}" required class="w-[136px] min-w-0 bg-cream-100 rounded-xl px-2 py-2 text-xs font-semibold text-stamp-700">
          <span aria-hidden="true" class="text-stamp-300">–</span>
          <label class="sr-only" for="dashboard-to">To date</label>
          <input id="dashboard-to" type="date" name="to" value="{{ $to }}" required class="w-[136px] min-w-0 bg-cream-100 rounded-xl px-2 py-2 text-xs font-semibold text-stamp-700">
        </div>
        <button type="submit" class="bg-stamp-500 hover:bg-stamp-600 text-cream-50 rounded-xl px-3 py-2 text-xs font-extrabold">Apply</button>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-extrabold text-stamp-500 px-2 py-2 hover:underline">Today</a>
      </form>
    </div>

    @include('admin.partials.expiry-alerts', ['compact' => true])

    <div class="flex items-center justify-between gap-3 mb-3">
      <h2 class="font-display font-bold text-lg text-stamp-700">At a glance</h2>
      <a href="{{ route('admin.reports.index', ['range' => 'custom', 'from' => $from, 'to' => $to]) }}" class="text-xs font-extrabold text-stamp-500 hover:underline">View reports &rarr;</a>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-[minmax(150px,auto)]">

      <!-- Hero: Sales in selected period -->
      <div class="col-span-2 row-span-1 lg:row-span-2 bg-gradient-to-br from-stamp-500 to-stamp-700 rounded-[2rem] shadow-soft p-6 md:p-7 text-cream-50 flex flex-col justify-between relative overflow-hidden hover:-translate-y-0.5 transition-transform">
        <a href="{{ route('admin.reports.index', ['range' => 'custom', 'from' => $from, 'to' => $to, 'type' => 'sales']) }}" aria-label="Open sales report for selected dates" class="absolute inset-0 z-10 rounded-[2rem] outline-none focus-visible:ring-4 focus-visible:ring-white/60"></a>
        <div class="absolute -bottom-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="absolute -top-10 -right-16 w-32 h-32 rounded-full bg-white/5"></div>
        <div class="relative flex items-center justify-between">
          <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-cream-50/70">Sales in selected period</p>
          <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <div class="relative">
          <p class="font-display font-bold text-4xl md:text-5xl mt-4">₱{{ number_format($periodSales, 2) }}</p>
          <p class="text-sm font-bold text-cream-50/80 mt-2">{{ $periodTransactionCount }} {{ Str::plural('transaction', $periodTransactionCount) }} in this period</p>
        </div>
        <a href="{{ route('pos.terminal') }}" class="relative z-20 mt-5 inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 transition-colors rounded-2xl px-4 py-2.5 text-sm font-bold w-fit">
          Open POS Terminal
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <!-- Transactions today -->
      <a href="{{ route('pos.transactions', ['from' => $from, 'to' => $to]) }}" class="bg-white rounded-3xl shadow-soft p-5 flex flex-col justify-between hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        </div>
        <div>
          <p class="font-display font-bold text-2xl text-stamp-700">{{ $periodTransactionCount }}</p>
          <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mt-0.5">Transactions in period</p>
        </div>
      </a>

      <!-- Low stock alerts -->
      <a href="{{ route('admin.inventory', ['stock' => 'low']) }}#items" class="{{ $lowStockCount ? 'bg-coral-50 ring-1 ring-coral-500/30' : 'bg-white' }} rounded-3xl shadow-soft p-5 flex flex-col justify-between hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        </div>
        <div>
          <p class="font-display font-bold text-2xl text-stamp-700">{{ $lowStockCount }}</p>
          <p class="text-[11px] font-extrabold uppercase tracking-wide text-coral-600 mt-0.5">Low Stock Alerts</p>
        </div>
      </a>

      <!-- Products -->
      <a href="{{ route('admin.products.index') }}" class="bg-white rounded-3xl shadow-soft p-5 flex flex-col justify-between hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
          <p class="font-display font-bold text-2xl text-stamp-700">{{ $totalProducts }}</p>
          <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mt-0.5">Menu Items</p>
        </div>
      </a>

      <!-- Staff -->
      <a href="{{ route('admin.users') }}" class="bg-white rounded-3xl shadow-soft p-5 flex flex-col justify-between hover:-translate-y-0.5 transition-transform">
        <div class="w-11 h-11 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
        </div>
        <div>
          <p class="font-display font-bold text-2xl text-stamp-700">{{ $totalStaff }}</p>
          <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mt-0.5">Staff Accounts</p>
        </div>
      </a>


    </div>

    <!-- Lists: two columns that each stack their cards, so nothing is stretched to match its neighbour -->
    <div class="grid lg:grid-cols-2 gap-4 mt-4 items-start">
      <div class="space-y-4">
      @if ($unavailableProducts->isNotEmpty())
        <!-- Products the POS can't sell right now -->
        <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6 ring-2 ring-coral-500/40">
          <div class="flex items-center justify-between mb-4 gap-2">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376C1.83 17.523 2.943 19.5 4.697 19.5h14.606c1.753 0 2.867-1.977 1.5-3.374L13.9 4.5c-.75-1.333-2.6-1.333-3.35 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
              </div>
              <h3 class="font-display font-bold text-stamp-700">Not available in POS</h3>
              <span class="bg-coral-50 text-coral-600 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $unavailableProducts->count() }}</span>
            </div>
            <a href="{{ route('admin.products.index', ['availability' => 'unavailable']) }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700 shrink-0">View all &rarr;</a>
          </div>
          <p class="text-xs font-semibold text-stamp-400 mb-3">These can't be sold at the terminal until the missing ingredient is restocked.</p>
          @foreach ($unavailableProducts->take(5) as $row)
            <a href="{{ route('admin.supply-purchases.index', ['restock' => $row->lacking->first()->id]) }}" title="Restock {{ $row->lacking->first()->name }}" class="block py-2 hover:bg-cream-50 -mx-2 px-2 rounded-xl transition-colors {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
              <p class="font-bold text-stamp-700 text-sm truncate">{{ $row->product->product_name }}</p>
              <p class="text-xs font-bold text-coral-500">Out of {{ $row->lacking->pluck('name')->join(', ', ' and ') }}</p>
            </a>
          @endforeach
          @if ($unavailableProducts->count() > 5)
            <p class="text-[11px] font-bold text-stamp-300 mt-2">and {{ $unavailableProducts->count() - 5 }} more</p>
          @endif
        </div>
      @endif

      <!-- Low stock list -->
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-bold text-stamp-700">Low Stock</h3>
          <a href="{{ route('admin.inventory') }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">Manage &rarr;</a>
        </div>
        @forelse ($lowStockItems as $item)
          <a href="{{ route('admin.inventory', ['stock' => 'low']) }}#items" class="flex items-center justify-between py-2 hover:bg-cream-50 -mx-2 px-2 rounded-xl transition-colors {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <div class="flex items-center gap-3">
              <span class="w-2 h-2 rounded-full bg-coral-500"></span>
              <p class="font-bold text-stamp-700 text-sm">{{ $item->name }}</p>
            </div>
            <p class="text-xs font-bold text-coral-500">{{ rtrim(rtrim(number_format($item->current_quantity, 2), '0'), '.') }} {{ $item->unit }} left</p>
          </a>
        @empty
          <div class="flex items-center gap-3 py-4">
            <div class="w-8 h-8 rounded-full bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-semibold text-stamp-400">All stocked up &mdash; nothing needs reordering.</p>
          </div>
        @endforelse
      </div>
      <!-- Top Sellers -->
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-mint-50 flex items-center justify-center text-mint-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 11.917L9.724 16.5 19 7.5"/></svg>
            </div>
            <h3 class="font-display font-bold text-stamp-700">Top Sellers</h3>
          </div>
          <span class="text-[10px] font-extrabold uppercase tracking-wide text-stamp-300">Selected period</span>
        </div>
        @forelse ($topSellers as $row)
          <a href="{{ route('admin.products.edit', $row->product) }}" class="flex items-center justify-between py-2 hover:bg-cream-50 -mx-2 px-2 rounded-xl transition-colors {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <div class="flex items-center gap-3 min-w-0">
              <span class="w-6 h-6 rounded-full bg-mint-50 text-mint-600 text-[11px] font-extrabold flex items-center justify-center shrink-0">{{ $loop->iteration }}</span>
              <p class="font-bold text-stamp-700 text-sm truncate">{{ $row->product->product_name }}</p>
            </div>
            <p class="text-xs font-bold text-mint-600 shrink-0">{{ $row->sold }} sold</p>
          </a>
        @empty
          <p class="text-sm font-semibold text-stamp-300 py-4">No product sales in this period.</p>
        @endforelse
      </div>
      </div>
      <div class="space-y-4">
      <!-- Recent sales -->
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-bold text-stamp-700">Recent Sales</h3>
          <a href="{{ route('pos.transactions', ['from' => $from, 'to' => $to]) }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">View all &rarr;</a>
        </div>
        @forelse ($recentTransactions as $transaction)
          <a href="{{ route('pos.transactions', ['from' => $from, 'to' => $to]) }}" class="flex items-center justify-between py-2 hover:bg-cream-50 -mx-2 px-2 rounded-xl transition-colors {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <div>
              <p class="font-bold text-stamp-700 text-sm">CB-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
              <p class="text-[11px] text-stamp-300 font-semibold">{{ $transaction->credential?->first_name ?? 'Unknown' }} &middot; {{ $transaction->transaction_date->diffForHumans() }}</p>
            </div>
            <p class="font-display font-bold text-stamp-700 text-sm">₱{{ number_format($transaction->total_amount, 2) }}</p>
          </a>
        @empty
          <p class="text-sm font-semibold text-stamp-300 py-4">No sales recorded in this period.</p>
        @endforelse
      </div>
      <!-- Slow Movers -->
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-coral-50 flex items-center justify-center text-coral-500">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376C1.83 17.523 2.943 19.5 4.697 19.5h14.606c1.753 0 2.867-1.977 1.5-3.374L13.9 4.5c-.75-1.333-2.6-1.333-3.35 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="font-display font-bold text-stamp-700">Slow Movers</h3>
          </div>
          <span class="text-[10px] font-extrabold uppercase tracking-wide text-stamp-300">Below average in period</span>
        </div>
        @forelse ($slowMovers as $row)
          <a href="{{ route('admin.products.edit', $row->product) }}" class="flex items-center justify-between py-2 hover:bg-cream-50 -mx-2 px-2 rounded-xl transition-colors {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <div class="flex items-center gap-3 min-w-0">
              <span class="w-2 h-2 rounded-full bg-coral-500 shrink-0"></span>
              <p class="font-bold text-stamp-700 text-sm truncate">{{ $row->product->product_name }}</p>
            </div>
            <p class="text-xs font-bold text-coral-500 shrink-0">{{ $row->sold }} sold</p>
          </a>
        @empty
          <div class="flex items-center gap-3 py-4">
            <div class="w-8 h-8 rounded-full bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-semibold text-stamp-400">Nothing is underperforming right now.</p>
          </div>
        @endforelse
      </div>
      </div>
    </div>
  </main>

  @include('admin.partials.toasts')

</body>
</html>

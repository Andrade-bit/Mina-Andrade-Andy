<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Reports</title>
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
<style>
  @media print {
    aside, .md\:hidden, .no-print { display: none !important; }
    body { display: block !important; background: #fff !important; }
    main { padding: 0 !important; overflow: visible !important; }
    .shadow-soft, .shadow-soft-sm { box-shadow: none !important; border: 1px solid #F1E6CC; }
  }
</style>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex">

  @include('admin.partials.sidebar', ['active' => 'reports'])

  <main class="flex-1 min-w-0 p-5 pt-20 md:p-8 overflow-y-auto">

    @php
      $peso = fn ($n) => '₱'.number_format($n, 2);
      $presets = ['today' => 'Today', 'week' => 'This week', 'month' => 'This month', 'last_month' => 'Last month'];
    @endphp

    <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
      <div>
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Admin &rsaquo; Reports</p>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Reports</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">
          {{ $from->format('M j, Y') }}@if ($from->toDateString() !== $to->toDateString()) &ndash; {{ $to->format('M j, Y') }}@endif
        </p>
      </div>
      <div class="flex items-center gap-2 no-print">
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Export CSV</a>
        <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Export PDF</button>
      </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-3xl shadow-soft p-4 md:p-5 mb-6 flex flex-wrap items-center gap-3 no-print">
      @foreach ($presets as $key => $label)
        <a href="{{ route('admin.reports.index', ['range' => $key]) }}" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-colors {{ $range === $key ? 'bg-stamp-500 text-cream-50' : 'bg-cream-100 text-stamp-600 hover:bg-cream-200' }}">{{ $label }}</a>
      @endforeach
      <span class="flex-1"></span>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2 flex items-center gap-2">
        <span class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">From</span>
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="bg-transparent outline-none text-sm text-stamp-700 font-semibold">
      </div>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2 flex items-center gap-2">
        <span class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">To</span>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="bg-transparent outline-none text-sm text-stamp-700 font-semibold">
      </div>
      <input type="hidden" name="range" value="custom">
      <button type="submit" class="px-5 py-2.5 rounded-2xl bg-stamp-500 hover:bg-stamp-600 text-cream-50 font-extrabold text-xs transition-colors">Apply</button>
    </form>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Gross sales</p>
        <p class="font-display font-bold text-xl md:text-2xl text-stamp-700 mt-1">{{ $peso($summary['gross']) }}</p>
        <p class="text-xs text-stamp-300 font-semibold">{{ $summary['sales_count'] }} completed {{ Str::plural('sale', $summary['sales_count']) }}</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Promo discounts</p>
        <p class="font-display font-bold text-xl md:text-2xl text-coral-600 mt-1">&minus;{{ $peso($summary['discounts']) }}</p>
        <p class="text-xs text-stamp-300 font-semibold">{{ $summary['promo_count'] }} {{ Str::plural('promo', $summary['promo_count']) }} used</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Voided</p>
        <p class="font-display font-bold text-xl md:text-2xl text-stamp-700 mt-1">{{ $peso($summary['voided']) }}</p>
        <p class="text-xs text-stamp-300 font-semibold">{{ $summary['voided_count'] }} voided &middot; not counted in sales</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total expenses</p>
        <p class="font-display font-bold text-xl md:text-2xl text-stamp-700 mt-1">{{ $peso($summary['expenses']) }}</p>
        <p class="text-xs text-stamp-300 font-semibold">includes Stock In purchases</p>
      </div>
      <div class="col-span-2 lg:col-span-1 rounded-3xl shadow-soft p-5 {{ $summary['profit'] >= 0 ? 'bg-stamp-500 text-cream-50' : 'bg-coral-600 text-cream-50' }}">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-cream-50/70">Net profit</p>
        <p class="font-display font-bold text-2xl md:text-3xl mt-1">{{ $summary['profit'] < 0 ? '−' : '' }}{{ $peso(abs($summary['profit'])) }}</p>
        <p class="text-xs text-cream-50/80 font-semibold">sales &minus; discounts &minus; expenses</p>
      </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-6">
      <div class="flex items-center justify-between flex-wrap gap-2 mb-5">
        <h3 class="font-display font-bold text-stamp-700 text-lg">Sales vs Expenses by day</h3>
        <div class="flex items-center gap-4 text-xs font-bold text-stamp-600">
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-stamp-500"></span>Sales</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-coral-500"></span>Expenses</span>
        </div>
      </div>
      @if ($chartMax <= 0)
        <p class="text-sm font-semibold text-stamp-300 py-10 text-center">No sales or expenses in this period.</p>
      @else
        <div class="overflow-x-auto">
          <div class="flex items-end gap-2 h-52 border-b-2 border-cream-200 px-1" style="min-width: {{ max(count($daily) * 28, 280) }}px">
            @foreach ($daily as $day)
              <div class="flex-1 h-full flex items-end gap-0.5" title="{{ $day['label'] }} — Sales {{ $peso($day['gross']) }}, Expenses {{ $peso($day['expenses']) }}">
                <div class="flex-1 bg-stamp-500 rounded-t-md" style="height: {{ round($day['gross'] / $chartMax * 100) }}%"></div>
                <div class="flex-1 bg-coral-500 rounded-t-md" style="height: {{ round($day['expenses'] / $chartMax * 100) }}%"></div>
              </div>
            @endforeach
          </div>
          <div class="flex gap-2 px-1 mt-1.5" style="min-width: {{ max(count($daily) * 28, 280) }}px">
            @foreach ($daily as $day)
              <span class="flex-1 text-center text-[10px] font-bold text-stamp-300 truncate">{{ count($daily) > 14 ? \Illuminate\Support\Str::after($day['label'], ' ') : $day['label'] }}</span>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <h3 class="font-display font-bold text-stamp-700 mb-3">Sales by payment method</h3>
        @forelse ($byPayment as $row)
          <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <span class="font-bold text-sm text-stamp-700">{{ $row->payment_method }} <span class="text-xs text-stamp-300 font-semibold">&middot; {{ $row->count }}</span></span>
            <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }}</span>
          </div>
        @empty
          <p class="text-sm font-semibold text-stamp-300 py-3">No sales in this period.</p>
        @endforelse
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <h3 class="font-display font-bold text-stamp-700 mb-3">Expenses by category</h3>
        @forelse ($byCategory as $row)
          <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <span class="font-bold text-sm text-stamp-700">{{ $row->category_name }}@if ($row->category_name === 'Inventory Purchases') <span class="text-[10px] font-extrabold uppercase text-mint-600 bg-mint-50 px-1.5 py-0.5 rounded-full ml-1">Stock In</span>@endif</span>
            <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }}</span>
          </div>
        @empty
          <p class="text-sm font-semibold text-stamp-300 py-3">No expenses in this period.</p>
        @endforelse
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
        <h3 class="font-display font-bold text-stamp-700 mb-3">Top products</h3>
        @forelse ($topProducts as $row)
          <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
            <span class="font-bold text-sm text-stamp-700 truncate"><span class="text-stamp-300">{{ $loop->iteration }}.</span> {{ $row->name }}</span>
            <span class="font-display font-bold text-sm text-mint-600 shrink-0">{{ $row->sold }} sold</span>
          </div>
        @empty
          <p class="text-sm font-semibold text-stamp-300 py-3">No sales in this period.</p>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-3">Daily breakdown</h3>
      <table class="w-full min-w-[640px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-3">Date</th>
            <th class="py-2 px-3">Sales</th>
            <th class="py-2 px-3">Discounts</th>
            <th class="py-2 px-3">Voids</th>
            <th class="py-2 px-3">Expenses</th>
            <th class="py-2 px-3">Net</th>
          </tr>
        </thead>
        <tbody>
          @foreach (array_reverse($daily) as $day)
            <tr class="border-t border-cream-200">
              <td class="py-3 px-3 text-sm font-bold text-stamp-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}</td>
              <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['gross']) }}</td>
              <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['discounts']) }}</td>
              <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['voided']) }}</td>
              <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['expenses']) }}</td>
              <td class="py-3 px-3 font-display font-bold text-sm {{ $day['net'] < 0 ? 'text-coral-600' : 'text-stamp-700' }}">{{ $day['net'] < 0 ? '−' : '' }}{{ $peso(abs($day['net'])) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>

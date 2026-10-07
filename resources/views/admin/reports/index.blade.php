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
      $qty = fn ($n) => rtrim(rtrim(number_format($n, 2), '0'), '.');
      $presets = ['today' => 'Today', 'week' => 'This week', 'month' => 'This month', 'last_month' => 'Last month'];
      $period = ['from' => $from->toDateString(), 'to' => $to->toDateString()];
      $keep = request()->only(['range', 'from', 'to']);
      $reportUrl = fn (string $kind) => route('admin.reports.index', [...$keep, 'type' => $kind]);
    @endphp

    <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
      <div>
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Admin &rsaquo; Reports &rsaquo; {{ $types[$type] }}</p>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">{{ $types[$type] }} Report</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">
          {{ $from->format('M j, Y') }}@if ($from->toDateString() !== $to->toDateString()) &ndash; {{ $to->format('M j, Y') }}@endif
        </p>
      </div>
      <div class="flex items-center gap-2 no-print">
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Export CSV</a>
        <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Export PDF</button>
      </div>
    </div>

    <nav class="flex items-center gap-2 mb-4 flex-wrap no-print" aria-label="Report type">
      @foreach ($types as $key => $label)
        <a href="{{ $reportUrl($key) }}" @if ($type === $key) aria-current="page" @endif class="px-5 py-2.5 rounded-2xl text-sm font-extrabold transition-all {{ $type === $key ? 'bg-stamp-500 text-cream-50 shadow-soft-btn' : 'bg-white text-stamp-600 shadow-soft-sm hover:bg-cream-100' }}">{{ $label }}</a>
      @endforeach
    </nav>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-3xl shadow-soft p-4 md:p-5 mb-6 flex flex-wrap items-center gap-3 no-print">
      <input type="hidden" name="type" value="{{ $type }}">
      @foreach ($presets as $key => $label)
        <a href="{{ route('admin.reports.index', ['range' => $key, 'type' => $type]) }}" class="px-4 py-2 rounded-2xl text-sm font-extrabold transition-colors {{ $range === $key ? 'bg-stamp-500 text-cream-50' : 'bg-cream-100 text-stamp-600 hover:bg-cream-200' }}">{{ $label }}</a>
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

    @include('admin.reports.partials.'.$type)
  </main>

  @include('admin.partials.toasts')

</body>
</html>

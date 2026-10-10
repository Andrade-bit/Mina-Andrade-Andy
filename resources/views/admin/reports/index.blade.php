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
      <details class="relative no-print">
        <summary class="cursor-pointer list-none rounded-xl bg-cream-100 px-4 py-2 text-stamp-600 font-bold" aria-label="Export options">⋮ <span class="text-sm">Export</span></summary>
        <div class="absolute right-0 top-full mt-2 z-20 min-w-40 rounded-2xl bg-white shadow-soft p-2">
          <a href="{{ route('admin.reports.export', request()->query()) }}" class="block px-3 py-2 rounded-xl text-sm font-bold hover:bg-cream-100">Export CSV</a>
          <button type="button" onclick="this.closest('details').open=false; window.print()" class="w-full text-left px-3 py-2 rounded-xl text-sm font-bold hover:bg-cream-100">Export PDF</button>
        </div>
      </details>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-2xl shadow-soft-sm p-3 mb-5 flex flex-wrap items-center gap-2 no-print">
      <label class="flex items-center gap-2 bg-cream-100 rounded-xl px-3 py-2 text-xs font-bold">Report
        <select name="type" aria-label="Report type" class="bg-transparent text-sm outline-none">
          @foreach ($types as $key => $label)<option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>@endforeach
        </select>
      </label>
      <input type="hidden" name="range" value="custom">
      <div class="flex max-w-full items-center gap-2">
        <label class="sr-only" for="reports-from">From date</label>
        <input id="reports-from" type="date" name="from" value="{{ $from->toDateString() }}" required class="w-[136px] min-w-0 bg-cream-100 rounded-xl px-2 py-2 text-xs font-semibold text-stamp-700">
        <span aria-hidden="true" class="text-stamp-300">–</span>
        <label class="sr-only" for="reports-to">To date</label>
        <input id="reports-to" type="date" name="to" value="{{ $to->toDateString() }}" required class="w-[136px] min-w-0 bg-cream-100 rounded-xl px-2 py-2 text-xs font-semibold text-stamp-700">
      </div>
      <button type="submit" class="bg-stamp-500 hover:bg-stamp-600 text-cream-50 rounded-xl px-3 py-2 text-xs font-extrabold">Apply</button>
      <a href="{{ route('admin.reports.index', ['type' => $type, 'range' => 'today']) }}" class="text-xs font-extrabold text-stamp-500 px-2 py-2 hover:underline">Today</a>
    </form>

    @include('admin.reports.partials.'.$type)
  </main>

  @include('admin.partials.toasts')

</body>
</html>

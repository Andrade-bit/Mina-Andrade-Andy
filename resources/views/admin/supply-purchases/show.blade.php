<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Purchase Details</title>
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

  @include('admin.partials.sidebar', ['active' => 'supply-purchases'])

  <!-- Main content -->
  <main data-narrow class="flex-1 min-w-0 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center gap-3 mb-6">
      <a href="{{ route('admin.supply-purchases.index') }}" class="w-10 h-10 rounded-xl bg-white shadow-soft-sm hover:bg-cream-100 flex items-center justify-center text-stamp-500 transition-colors shrink-0">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Purchase #{{ $purchase->id }}</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">{{ $purchase->purchase_date->format('F j, Y') }}</p>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <a href="{{ route('admin.suppliers.index') }}" class="block bg-white rounded-3xl shadow-soft p-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Bought From</p>
        <p class="font-display font-bold text-stamp-700">{{ $purchase->supplier->supplier_name ?? $purchase->purchase_source ?? 'Unspecified' }}</p>
      </a>
      <a href="{{ route('admin.supply-purchases.index', ['payment_method' => $purchase->payment_method]) }}" class="block bg-white rounded-3xl shadow-soft p-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Payment Method</p>
        <p class="font-display font-bold text-stamp-700">{{ $purchase->payment_method }}</p>
      </a>
      <a href="{{ route('admin.suppliers.index') }}" class="block bg-white rounded-3xl shadow-soft p-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Payment Terms</p>
        <p class="font-display font-bold text-stamp-700">{{ $purchase->payment_terms ?? '—' }}</p>
      </a>
      <a href="{{ route('admin.expenses.index') }}" class="block bg-white rounded-3xl shadow-soft p-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Total Spent</p>
        <p class="font-display font-bold text-stamp-700">₱{{ number_format($purchase->total_amount, 2) }}</p>
      </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-4">Items Purchased</h3>
      <table class="w-full min-w-[480px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-3">Item</th>
            <th class="py-2 px-3">Quantity</th>
            <th class="py-2 px-3">Unit Cost</th>
            <th class="py-2 px-3">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($purchase->items as $item)
            <tr class="border-b border-cream-200 last:border-0">
              <td class="py-3 px-3 font-bold text-stamp-700 text-sm">{{ $item->name }}</td>
              <td class="py-3 px-3 text-stamp-500 text-sm">{{ rtrim(rtrim(number_format($item->pivot->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
              <td class="py-3 px-3 text-stamp-500 text-sm">₱{{ number_format($item->pivot->unit_cost, $item->pivot->unit_cost < 1 ? 4 : 2) }}</td>
              <td class="py-3 px-3 font-display font-bold text-stamp-700">₱{{ number_format($item->pivot->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </main>

  @include('admin.partials.toasts')

</body>
</html>

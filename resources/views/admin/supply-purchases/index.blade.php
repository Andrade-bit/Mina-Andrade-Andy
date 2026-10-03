<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Supply Purchases</title>
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
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Supply Purchases</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">History of stock bought from suppliers or bought yourself</p>
      </div>
      <a href="{{ route('admin.inventory') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Record Purchase
      </a>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-4">Date</th>
            <th class="py-2 px-4">Bought From</th>
            <th class="py-2 px-4">Items</th>
            <th class="py-2 px-4">Payment</th>
            <th class="py-2 px-4">Total</th>
            <th class="py-2 px-4"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($purchases as $purchase)
            <tr class="border-b border-cream-200 last:border-0">
              <td class="py-3 px-4 text-stamp-500 text-sm whitespace-nowrap">{{ $purchase->purchase_date->format('M j, Y') }}</td>
              <td class="py-3 px-4 font-bold text-stamp-700 text-sm">{{ $purchase->supplier->supplier_name ?? $purchase->purchase_source ?? 'Unspecified' }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $purchase->items->count() }} {{ Str::plural('item', $purchase->items->count()) }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $purchase->payment_method }}</td>
              <td class="py-3 px-4 font-display font-bold text-stamp-700">₱{{ number_format($purchase->total_amount, 2) }}</td>
              <td class="py-3 px-4">
                <a href="{{ route('admin.supply-purchases.show', $purchase) }}" class="text-stamp-500 text-xs font-bold hover:underline">View &rarr;</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-10 text-center text-sm font-semibold text-stamp-300">No purchases recorded yet. Use "Stock In" from the Inventory page to record one.</td></tr>
          @endforelse
        </tbody>
      </table>

      @include('admin.partials.pagination', ['paginator' => $purchases])
    </div>
  </main>

</body>
</html>

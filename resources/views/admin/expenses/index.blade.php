<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Expenses</title>
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

  @include('admin.partials.sidebar', ['active' => 'expenses'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Expenses</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Track rent, utilities, and other shop expenses</p>
      </div>
      <a href="{{ route('admin.expenses.create') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Record Expense
      </a>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-3xl shadow-soft p-5 mb-6 flex items-center gap-4 max-w-xs">
      <div class="w-12 h-12 rounded-2xl bg-coral-50 flex items-center justify-center text-coral-500 shrink-0">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 012 2v16l-4-2-3 2-3-2-3 2-3-2-3 2V5a2 2 0 012-2z"/></svg>
      </div>
      <div>
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">This Month</p>
        <p class="font-display font-bold text-2xl text-stamp-700">₱{{ number_format($totalThisMonth, 2) }}</p>
      </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <table class="w-full min-w-[640px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-4">Date</th>
            <th class="py-2 px-4">Description</th>
            <th class="py-2 px-4">Category</th>
            <th class="py-2 px-4">Payment</th>
            <th class="py-2 px-4">Amount</th>
            <th class="py-2 px-4"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($expenses as $expense)
            <tr class="border-b border-cream-200 last:border-0">
              <td class="py-3 px-4 text-stamp-500 text-sm whitespace-nowrap">{{ $expense->expense_date->format('M j, Y') }}</td>
              <td class="py-3 px-4 font-bold text-stamp-700 text-sm">
                {{ $expense->description }}
                @if ($expense->supply_purchase_id)
                  <a href="{{ route('admin.supply-purchases.show', $expense->supply_purchase_id) }}" class="inline-flex items-center ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-mint-50 text-mint-600 hover:bg-mint-100 transition-colors align-middle">Stock In</a>
                @endif
              </td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $expense->expenseCategory->category_name ?? 'Uncategorized' }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $expense->payment_method }}</td>
              <td class="py-3 px-4 font-display font-bold text-stamp-700">₱{{ number_format($expense->amount, 2) }}</td>
              <td class="py-3 px-4">
                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" onsubmit="return confirm('Remove this expense?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-coral-500 text-xs font-bold hover:underline">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-10 text-center text-sm font-semibold text-stamp-300">No expenses recorded yet.</td></tr>
          @endforelse
        </tbody>
      </table>

      @include('admin.partials.pagination', ['paginator' => $expenses])
    </div>
  </main>

</body>
</html>

@php
  $biggest = $byCategory->first();
  $total = $summary['expenses'];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  @include('admin.reports.partials.kpi', ['href' => route('admin.expenses.index'), 'tone' => 'primary', 'label' => 'Total expenses', 'value' => $peso($total), 'sub' => 'everything paid out in this period'])
  @include('admin.reports.partials.kpi', ['href' => '#expense-list', 'label' => 'Entries', 'value' => $expenseCount, 'sub' => Str::plural('expense', $expenseCount).' recorded'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.expense-categories.index'), 'label' => 'Biggest category', 'value' => $biggest?->category_name ?? '—', 'sub' => $biggest ? $peso($biggest->total) : 'no expenses yet'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.supply-purchases.index'), 'label' => 'Stock purchases', 'value' => $peso($purchaseSpend), 'sub' => 'from Supply Purchases'])
</div>

@include('admin.reports.partials.chart', [
  'title' => 'Expenses by day',
  'bars' => [['key' => 'expenses', 'class' => 'bg-coral-500', 'label' => 'Expenses']],
  'emptyText' => 'No expenses in this period.',
])

<div class="grid md:grid-cols-2 gap-4 mb-6">
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
    <h3 class="font-display font-bold text-stamp-700 mb-3">By category</h3>
    @forelse ($byCategory as $row)
      <div class="py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
        <div class="flex items-center justify-between">
          <span class="font-bold text-sm text-stamp-700">{{ $row->category_name }}@if ($row->category_name === 'Inventory Purchases') <span class="text-[10px] font-extrabold uppercase text-mint-600 bg-mint-50 px-1.5 py-0.5 rounded-full ml-1">Purchases</span>@endif</span>
          <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }} <span class="text-xs text-stamp-300 font-semibold">&middot; {{ $total > 0 ? round($row->total / $total * 100) : 0 }}%</span></span>
        </div>
        <div class="mt-1.5 h-1.5 rounded-full bg-cream-100 overflow-hidden"><div class="h-full rounded-full bg-coral-500" style="width: {{ $total > 0 ? round($row->total / $total * 100) : 0 }}%"></div></div>
      </div>
    @empty
      <p class="text-sm font-semibold text-stamp-300 py-3">No expenses in this period.</p>
    @endforelse
  </div>
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
    <h3 class="font-display font-bold text-stamp-700 mb-3">By payment method</h3>
    @forelse ($expenseByPayment as $row)
      <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
        <span class="font-bold text-sm text-stamp-700">{{ $row->payment_method }} <span class="text-xs text-stamp-300 font-semibold">&middot; {{ $row->count }}</span></span>
        <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }}</span>
      </div>
    @empty
      <p class="text-sm font-semibold text-stamp-300 py-3">No expenses in this period.</p>
    @endforelse
  </div>
</div>

<div id="expense-list" class="scroll-mt-20 bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
  <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
    <h3 class="font-display font-bold text-stamp-700 text-lg">Expense list</h3>
    <a href="{{ route('admin.expenses.index') }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">Manage expenses &rarr;</a>
  </div>
  <table class="w-full min-w-[640px] text-left border-collapse">
    <thead>
      <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
        <th class="py-2 px-3">Date</th>
        <th class="py-2 px-3">Category</th>
        <th class="py-2 px-3">Description</th>
        <th class="py-2 px-3">Payment</th>
        <th class="py-2 px-3 text-right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($expenseRows as $expense)
        <tr class="border-t border-cream-200">
          <td class="py-3 px-3 text-sm font-bold text-stamp-700 whitespace-nowrap">{{ $expense->expense_date->format('M j, Y') }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $expense->expenseCategory?->category_name ?? 'Uncategorized' }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $expense->description }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $expense->payment_method }}</td>
          <td class="py-3 px-3 text-right font-display font-bold text-sm text-stamp-700">{{ $peso($expense->amount) }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="py-8 text-center text-sm font-semibold text-stamp-300">No expenses in this period.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if ($expenseCount > $expenseRows->count())
    <p class="text-xs font-semibold text-stamp-300 mt-3">Showing the latest {{ $expenseRows->count() }} of {{ $expenseCount }}. Export CSV for the full list.</p>
  @endif
</div>

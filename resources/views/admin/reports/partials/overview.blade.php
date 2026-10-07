<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
  @include('admin.reports.partials.kpi', ['href' => $reportUrl('sales'), 'label' => 'Gross sales', 'value' => $peso($summary['gross']), 'sub' => $summary['sales_count'].' completed '.Str::plural('sale', $summary['sales_count'])])
  @include('admin.reports.partials.kpi', ['href' => route('admin.promos.index'), 'label' => 'Promo discounts', 'value' => '−'.$peso($summary['discounts']), 'valueClass' => 'text-coral-600', 'sub' => $summary['promo_count'].' '.Str::plural('promo', $summary['promo_count']).' used'])
  @include('admin.reports.partials.kpi', ['href' => route('pos.transactions', [...$period, 'status' => 'voided']), 'label' => 'Voided', 'value' => $peso($summary['voided']), 'sub' => $summary['voided_count'].' voided · not counted in sales'])
  @include('admin.reports.partials.kpi', ['href' => $reportUrl('expenses'), 'label' => 'Total expenses', 'value' => $peso($summary['expenses']), 'sub' => 'includes purchases'])
  @include('admin.reports.partials.kpi', ['href' => '#daily', 'tone' => $summary['profit'] >= 0 ? 'primary' : 'danger', 'class' => 'col-span-2 lg:col-span-1', 'label' => 'Net profit', 'value' => ($summary['profit'] < 0 ? '−' : '').$peso(abs($summary['profit'])), 'sub' => 'sales − discounts − expenses'])
</div>

@include('admin.reports.partials.chart', [
  'title' => 'Sales vs Expenses by day',
  'bars' => [['key' => 'gross', 'class' => 'bg-stamp-500', 'label' => 'Sales'], ['key' => 'expenses', 'class' => 'bg-coral-500', 'label' => 'Expenses']],
  'emptyText' => 'No sales or expenses in this period.',
])

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
        <span class="font-bold text-sm text-stamp-700">{{ $row->category_name }}@if ($row->category_name === 'Inventory Purchases') <span class="text-[10px] font-extrabold uppercase text-mint-600 bg-mint-50 px-1.5 py-0.5 rounded-full ml-1">Purchases</span>@endif</span>
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

<div id="daily" class="scroll-mt-20 bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
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
          <td class="py-3 px-3 text-sm font-bold text-stamp-700 whitespace-nowrap">{{ Carbon\Carbon::parse($day['date'])->format('M j, Y') }}</td>
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

@php
  $transactionsUrl = route('pos.transactions', $period);
  $averageOrder = $summary['sales_count'] > 0 ? $summary['net_sales'] / $summary['sales_count'] : 0;
@endphp

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
  @include('admin.reports.partials.kpi', ['href' => '#daily', 'label' => 'Gross sales', 'value' => $peso($summary['gross']), 'sub' => 'before promo discounts'])
  @include('admin.reports.partials.kpi', ['href' => $transactionsUrl, 'tone' => 'primary', 'label' => 'Net sales', 'value' => $peso($summary['net_sales']), 'sub' => 'after discounts, voids left out'])
  @include('admin.reports.partials.kpi', ['href' => $transactionsUrl, 'label' => 'Transactions', 'value' => $summary['sales_count'], 'sub' => 'completed '.Str::plural('sale', $summary['sales_count'])])
  @include('admin.reports.partials.kpi', ['href' => '#recent-sales', 'label' => 'Average order', 'value' => $peso($averageOrder), 'sub' => 'net sales per transaction'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.promos.index'), 'label' => 'Promo discounts', 'value' => '−'.$peso($summary['discounts']), 'valueClass' => 'text-coral-600', 'sub' => $summary['promo_count'].' '.Str::plural('promo', $summary['promo_count']).' used'])
  @include('admin.reports.partials.kpi', ['href' => route('pos.transactions', [...$period, 'status' => 'voided']), 'label' => 'Voided', 'value' => $peso($summary['voided']), 'sub' => $summary['voided_count'].' voided · not counted'])
</div>

@include('admin.reports.partials.chart', [
  'title' => 'Sales by day',
  'bars' => [['key' => 'gross', 'class' => 'bg-stamp-500', 'label' => 'Sales']],
  'emptyText' => 'No sales in this period.',
])

<div class="grid md:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
    <h3 class="font-display font-bold text-stamp-700 mb-3">By payment method</h3>
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
    <h3 class="font-display font-bold text-stamp-700 mb-3">By category</h3>
    @forelse ($salesByCategory as $row)
      <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
        <span class="font-bold text-sm text-stamp-700">{{ $row->name }} <span class="text-xs text-stamp-300 font-semibold">&middot; {{ (int) $row->qty }} sold</span></span>
        <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }}</span>
      </div>
    @empty
      <p class="text-sm font-semibold text-stamp-300 py-3">No sales in this period.</p>
    @endforelse
  </div>
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6">
    <h3 class="font-display font-bold text-stamp-700 mb-3">By staff</h3>
    @forelse ($salesByStaff as $row)
      <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
        <span class="font-bold text-sm text-stamp-700">{{ $row->name }} <span class="text-xs text-stamp-300 font-semibold">&middot; {{ $row->count }}</span></span>
        <span class="font-display font-bold text-sm text-stamp-700">{{ $peso($row->total) }}</span>
      </div>
    @empty
      <p class="text-sm font-semibold text-stamp-300 py-3">No sales in this period.</p>
    @endforelse
  </div>
</div>

<div class="grid lg:grid-cols-5 gap-4 mb-6">
  <div class="lg:col-span-2 bg-white rounded-3xl shadow-soft p-5 md:p-6">
    <h3 class="font-display font-bold text-stamp-700 mb-3">Top products</h3>
    @forelse ($topProductsFull as $row)
      <div class="flex items-center justify-between py-2.5 {{ ! $loop->last ? 'border-b border-cream-100' : '' }}">
        <span class="font-bold text-sm text-stamp-700 truncate"><span class="text-stamp-300">{{ $loop->iteration }}.</span> {{ $row->name }}</span>
        <span class="font-display font-bold text-sm text-mint-600 shrink-0">{{ $row->sold }} sold</span>
      </div>
    @empty
      <p class="text-sm font-semibold text-stamp-300 py-3">No sales in this period.</p>
    @endforelse
  </div>
  <div id="recent-sales" class="scroll-mt-20 lg:col-span-3 bg-white rounded-3xl shadow-soft p-5 md:p-6 overflow-x-auto">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display font-bold text-stamp-700">Latest sales</h3>
      <a href="{{ $transactionsUrl }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">View all &rarr;</a>
    </div>
    <table class="w-full min-w-[420px] text-left border-collapse">
      <tbody>
        @forelse ($recentSales as $sale)
          <tr class="border-t border-cream-100 first:border-0">
            <td class="py-2.5 pr-3 text-sm font-bold text-stamp-700 whitespace-nowrap">CB-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="py-2.5 pr-3 text-xs font-semibold text-stamp-400 whitespace-nowrap">{{ $sale->transaction_date->timezone('Asia/Manila')->format('M j, Y, g:i A') }}</td>
            <td class="py-2.5 pr-3 text-xs font-semibold text-stamp-400">{{ $sale->credential?->first_name ?? 'Unknown' }} &middot; {{ $sale->payment_method }}</td>
            <td class="py-2.5 text-right font-display font-bold text-sm {{ $sale->status === 'voided' ? 'text-stamp-300 line-through' : 'text-stamp-700' }}">{{ $peso($sale->total_amount) }}</td>
          </tr>
        @empty
          <tr><td class="py-3 text-sm font-semibold text-stamp-300">No sales in this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="daily" class="scroll-mt-20 bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
  <h3 class="font-display font-bold text-stamp-700 text-lg mb-3">Daily sales</h3>
  <table class="w-full min-w-[560px] text-left border-collapse">
    <thead>
      <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
        <th class="py-2 px-3">Date</th>
        <th class="py-2 px-3">Gross sales</th>
        <th class="py-2 px-3">Discounts</th>
        <th class="py-2 px-3">Voids</th>
        <th class="py-2 px-3">Net sales</th>
      </tr>
    </thead>
    <tbody>
      @foreach (array_reverse($daily) as $day)
        <tr class="border-t border-cream-200">
          <td class="py-3 px-3 text-sm font-bold text-stamp-700 whitespace-nowrap">{{ Carbon\Carbon::parse($day['date'])->format('M j, Y') }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['gross']) }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['discounts']) }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $peso($day['voided']) }}</td>
          <td class="py-3 px-3 font-display font-bold text-sm text-stamp-700">{{ $peso($day['gross'] - $day['discounts']) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

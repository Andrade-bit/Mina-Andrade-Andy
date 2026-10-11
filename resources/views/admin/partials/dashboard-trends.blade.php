<section aria-label="Product trends and peak hours" class="grid md:grid-cols-2 gap-4 mt-4">
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6 min-w-0">
    <h2 class="font-display font-bold text-lg text-stamp-700">Trending products</h2>
    <p class="text-xs text-stamp-500">Top 5 by units sold · {{ $periodLabel }}</p>
    <div class="space-y-4 mt-5">
      @forelse ($trendingProducts as $row)
        <a href="{{ route('admin.products.edit', $row->product) }}" class="block group">
          <div class="flex justify-between gap-3 text-sm mb-1.5">
            <span class="font-bold text-stamp-700 truncate group-hover:underline">{{ $row->product->product_name }}</span>
            <span class="text-stamp-500 font-bold shrink-0">{{ $row->sold }} sold</span>
          </div>
          <div class="h-3 bg-stamp-50 rounded-full overflow-hidden" role="img" aria-label="{{ $row->product->product_name }}: {{ $row->sold }} units sold">
            <div class="h-full bg-stamp-500 rounded-full" style="width: {{ $row->sold / max(1, $trendingProducts->max('sold')) * 100 }}%"></div>
          </div>
        </a>
      @empty
        <p class="text-sm text-stamp-500 py-8">No product sales in this period.</p>
      @endforelse
    </div>
  </div>
  @php
    $maximum = max(1, max(array_column($peakHours, 'transactions')));
    $busyHours = collect($peakHours)->where('transactions', $maximum)->pluck('label');
    $hasSales = array_sum(array_column($peakHours, 'transactions')) > 0;
  @endphp
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6 min-w-0">
    <h2 class="font-display font-bold text-lg text-stamp-700">Peak hours</h2>
    <p class="text-xs text-stamp-500">Completed transactions by hour · {{ $periodLabel }}</p>
    <svg viewBox="0 0 560 210" class="w-full mt-3" role="img" aria-label="Transactions by hour across the selected dates">
      @foreach ([0, 0.5, 1] as $step)
        <line x1="36" y1="{{ 170 - $step * 130 }}" x2="544" y2="{{ 170 - $step * 130 }}" stroke="#EAF3FA" />
        <text x="30" y="{{ 174 - $step * 130 }}" text-anchor="end" font-size="10" fill="#526777">{{ $maximum * $step }}</text>
      @endforeach
      @foreach ($peakHours as $i => $point)
        <rect x="{{ 40 + $i * 21 }}" y="{{ 170 - $point['transactions'] * 130 / $maximum }}" width="14" height="{{ $point['transactions'] * 130 / $maximum }}" rx="3" fill="{{ $point['transactions'] === $maximum ? '#3F7457' : '#6FAE8B' }}"><title>{{ $point['label'] }}: {{ $point['transactions'] }} transactions</title></rect>
        @if ($i % 4 === 0 || $i === 23)
          <text x="{{ 47 + $i * 21 }}" y="195" text-anchor="middle" font-size="10" fill="#526777">{{ $point['label'] }}</text>
        @endif
      @endforeach
    </svg>
    <p class="text-xs font-semibold text-stamp-500">{{ $hasSales ? 'Busiest: '.$busyHours->join(', ').' · '.$maximum.' '.Str::plural('transaction', $maximum).' per hour across selected dates' : 'No completed sales in this period.' }}</p>
  </div>
</section>

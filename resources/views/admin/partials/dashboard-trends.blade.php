<section aria-label="Sales trends" class="grid md:grid-cols-2 gap-4 mt-4">
  @foreach (['revenue' => 'Sales trend', 'transactions' => 'Transaction trend'] as $metric => $title)
    @php
      $points = $salesTrend['points'];
      $maximum = max(1, max(array_column($points, $metric)));
      $count = count($points);
      $coordinates = collect($points)->map(fn ($point, $i) => (48 + $i * 484 / max(1, $count - 1)).','. (170 - $point[$metric] * 130 / $maximum))->join(' ');
      $tickEvery = max(1, (int) ceil($count / 5));
    @endphp
    <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6 min-w-0">
      <div class="flex items-center justify-between gap-3 mb-1">
        <h2 class="font-display font-bold text-lg text-stamp-700">{{ $title }}</h2>
        <span class="text-xs font-bold text-stamp-500">{{ $salesTrend['interval'] }}</span>
      </div>
      <p class="text-xs text-stamp-500">{{ $periodLabel }} · Completed sales</p>
      <svg viewBox="0 0 560 210" class="w-full mt-3" role="img" aria-label="{{ $title }} for {{ $periodLabel }}">
        @foreach ([0, 0.5, 1] as $step)
          <line x1="48" y1="{{ 170 - $step * 130 }}" x2="532" y2="{{ 170 - $step * 130 }}" stroke="#EAF3FA" />
          <text x="42" y="{{ 174 - $step * 130 }}" text-anchor="end" font-size="10" fill="#526777">{{ $metric === 'revenue' ? '₱'.number_format($maximum * $step, 0) : number_format($maximum * $step, $step === 0.5 && $maximum < 2 ? 1 : 0) }}</text>
        @endforeach
        <polyline points="{{ $coordinates }}" fill="none" stroke="{{ $metric === 'revenue' ? '#2F6690' : '#3F7457' }}" stroke-width="3" stroke-linejoin="round" />
        @foreach ($points as $i => $point)
          <circle cx="{{ 48 + $i * 484 / max(1, $count - 1) }}" cy="{{ 170 - $point[$metric] * 130 / $maximum }}" r="{{ $count > 40 ? 2 : 3 }}" fill="{{ $metric === 'revenue' ? '#2F6690' : '#3F7457' }}"><title>{{ $point['label'] }}: {{ $metric === 'revenue' ? '₱'.number_format($point[$metric], 2) : $point[$metric].' transactions' }}</title></circle>
          @if ($i % $tickEvery === 0 || $i === $count - 1)
            <text x="{{ 48 + $i * 484 / max(1, $count - 1) }}" y="195" text-anchor="middle" font-size="10" fill="#526777">{{ $point['label'] }}</text>
          @endif
        @endforeach
      </svg>
      @if (array_sum(array_column($points, $metric)) === 0 || array_sum(array_column($points, $metric)) === 0.0)
        <p class="text-xs text-stamp-500">No completed sales in this period.</p>
      @endif
    </div>
  @endforeach
</section>

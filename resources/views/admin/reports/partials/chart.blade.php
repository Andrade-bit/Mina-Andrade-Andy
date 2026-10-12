@php
  $max = collect($daily)->max(fn ($day) => max(array_map(fn ($bar) => $day[$bar['key']], $bars)));
  $colors = ['bg-stamp-500' => '#2F6690', 'bg-coral-500' => '#E0776B', 'bg-mint-500' => '#6FAE8B'];
  $count = max(1, count($daily));
  $groupWidth = 740 / $count;
  $barWidth = max(0.5, ($groupWidth - min(5, $groupWidth / 4)) / count($bars));
  $tickEvery = max(1, (int) ceil($count / 8));
@endphp
<div class="report-chart bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-6">
  <div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h3 class="font-display font-bold text-stamp-700 text-lg">{{ $title }}</h3>
    <div class="flex items-center gap-4 text-xs font-bold text-stamp-600">
      @foreach ($bars as $bar)
        <span class="flex items-center gap-1.5"><svg width="10" height="10" aria-hidden="true"><rect width="10" height="10" rx="2" fill="{{ $colors[$bar['class']] ?? '#2F6690' }}" /></svg>{{ $bar['label'] }}</span>
      @endforeach
    </div>
  </div>
  @if ($max <= 0)
    <p class="text-sm font-semibold text-stamp-500 py-10 text-center">{{ $emptyText }}</p>
  @else
    <svg viewBox="0 0 800 270" class="report-chart-svg w-full" role="img" aria-label="{{ $title }}">
      @foreach ([0, 0.5, 1] as $step)
        <line x1="40" y1="{{ 230 - $step * 200 }}" x2="780" y2="{{ 230 - $step * 200 }}" stroke="#D3E6F3" stroke-width="1" />
      @endforeach
      @foreach ($daily as $i => $day)
        @foreach ($bars as $j => $bar)
          @php $height = $day[$bar['key']] / $max * 200; @endphp
          <rect x="{{ 40 + $i * $groupWidth + $j * $barWidth }}" y="{{ 230 - $height }}" width="{{ $barWidth }}" height="{{ $height }}" rx="{{ min(3, $barWidth / 3) }}" fill="{{ $colors[$bar['class']] ?? '#2F6690' }}"><title>{{ $day['label'] }} · {{ $bar['label'] }}: {{ $peso($day[$bar['key']]) }}</title></rect>
        @endforeach
        @if ($i % $tickEvery === 0 || ($i === $count - 1 && $i % $tickEvery >= ceil($tickEvery / 2)))
          <text x="{{ 40 + ($i + 0.5) * $groupWidth }}" y="250" text-anchor="middle" font-size="11" fill="#24506F">{{ $day['label'] }}</text>
        @endif
      @endforeach
    </svg>
  @endif
</div>

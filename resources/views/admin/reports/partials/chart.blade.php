{{--
  Bars per day. Pass: daily, title, bars = [['key' => 'gross', 'class' => 'bg-stamp-500', 'label' => 'Sales'], ...],
  and emptyText. The scale is set by the tallest bar shown.
--}}
@php
  $max = collect($daily)->max(fn ($day) => max(array_map(fn ($bar) => $day[$bar['key']], $bars)));
  $width = max(count($daily) * 28, 280);
@endphp
<div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-6">
  <div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h3 class="font-display font-bold text-stamp-700 text-lg">{{ $title }}</h3>
    <div class="flex items-center gap-4 text-xs font-bold text-stamp-600">
      @foreach ($bars as $bar)
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm {{ $bar['class'] }}"></span>{{ $bar['label'] }}</span>
      @endforeach
    </div>
  </div>
  @if ($max <= 0)
    <p class="text-sm font-semibold text-stamp-300 py-10 text-center">{{ $emptyText }}</p>
  @else
    <div class="overflow-x-auto">
      <div class="flex items-end gap-2 h-52 border-b-2 border-cream-200 px-1" style="min-width: {{ $width }}px">
        @foreach ($daily as $day)
          <div class="flex-1 h-full flex items-end gap-0.5" title="{{ $day['label'] }}: @foreach ($bars as $bar){{ $bar['label'] }} {{ $peso($day[$bar['key']]) }}{{ ! $loop->last ? ', ' : '' }}@endforeach">
            @foreach ($bars as $bar)
              <div class="flex-1 {{ $bar['class'] }} rounded-t-md" style="height: {{ round($day[$bar['key']] / $max * 100) }}%"></div>
            @endforeach
          </div>
        @endforeach
      </div>
      <div class="flex gap-2 px-1 mt-1.5" style="min-width: {{ $width }}px">
        @foreach ($daily as $day)
          <span class="flex-1 text-center text-[10px] font-bold text-stamp-300 truncate">{{ count($daily) > 14 ? Illuminate\Support\Str::after($day['label'], ' ') : $day['label'] }}</span>
        @endforeach
      </div>
    </div>
  @endif
</div>

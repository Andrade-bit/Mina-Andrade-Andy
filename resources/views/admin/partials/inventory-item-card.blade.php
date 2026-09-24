@php
  $isLow = $item->current_quantity <= $item->reorder_level;
  $displayQty = rtrim(rtrim(number_format($item->current_quantity, 2), '0'), '.');
  $displayReorder = rtrim(rtrim(number_format($item->reorder_level, 2), '0'), '.');
@endphp

<div class="bg-white rounded-3xl shadow-soft p-5">
  <div class="flex items-start justify-between mb-3">
    <div>
      <p class="font-extrabold text-stamp-700">{{ $item->name }}</p>
      <p class="text-[11px] text-stamp-300 font-bold uppercase tracking-wide">Reorder at {{ $displayReorder }} {{ $item->unit }}</p>
    </div>
    @if ($isLow)
      <span class="bg-coral-50 text-coral-600 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full">Low Stock</span>
    @endif
  </div>
  <p class="font-display font-bold text-3xl text-stamp-700 mb-4">{{ $displayQty }} <span class="text-sm font-body font-bold text-stamp-300">{{ $item->unit }}</span></p>
  <div class="flex gap-2">
    <button type="button" onclick="openStockIn({{ $item->id }}, {{ Illuminate\Support\Js::from($item->name) }})" class="flex-1 py-2.5 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-6 6m6-6l6 6"/></svg>
      Stock In
    </button>
    <button type="button" onclick="openStockOut({{ $item->id }}, {{ Illuminate\Support\Js::from($item->name) }}, {{ Illuminate\Support\Js::from($displayQty) }}, {{ Illuminate\Support\Js::from($item->unit) }})" class="flex-1 py-2.5 rounded-xl bg-coral-50 hover:bg-coral-500 hover:text-cream-50 text-coral-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
      Stock Out
    </button>
  </div>
</div>

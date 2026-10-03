@php
  $isLow = $item->current_quantity <= $item->reorder_level;
  $displayQty = rtrim(rtrim(number_format($item->current_quantity, 2), '0'), '.');
  $displayReorder = rtrim(rtrim(number_format($item->reorder_level, 2), '0'), '.');
@endphp

<div class="bg-white rounded-3xl shadow-soft p-5 relative {{ $isLow ? 'ring-2 ring-coral-500/60' : '' }}">
  @if ($isLow)
    <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-coral-500 text-cream-50 flex items-center justify-center shadow-soft-sm" title="Needs reordering">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376C1.83 17.523 2.943 19.5 4.697 19.5h14.606c1.753 0 2.867-1.977 1.5-3.374L13.9 4.5c-.75-1.333-2.6-1.333-3.35 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
    </span>
  @endif
  <div class="flex items-start justify-between mb-3">
    <div class="flex items-center gap-3 min-w-0">
      @if ($item->image)
        <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-xl object-cover shrink-0">
      @endif
      <div class="min-w-0">
        <p class="font-extrabold text-stamp-700 truncate">{{ $item->name }}</p>
        <p class="text-[11px] text-stamp-300 font-bold uppercase tracking-wide">Reorder at {{ $displayReorder }} {{ $item->unit }}</p>
      </div>
    </div>
    @if ($isLow)
      <span class="bg-coral-50 text-coral-600 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full shrink-0">Low Stock</span>
    @endif
  </div>
  <p class="font-display font-bold text-3xl {{ $isLow ? 'text-coral-600' : 'text-stamp-700' }} mb-4">{{ $displayQty }} <span class="text-sm font-body font-bold text-stamp-300">{{ $item->unit }}</span></p>
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

@php
  $isArchived = $item->trashed();
  $isLow = ! $isArchived && $item->current_quantity <= $item->reorder_level;
  $displayQty = rtrim(rtrim(number_format($item->current_quantity, 2), '0'), '.');
  $displayReorder = rtrim(rtrim(number_format($item->reorder_level, 2), '0'), '.');
@endphp

<div class="bg-white rounded-3xl shadow-soft p-5 relative {{ $isLow ? 'ring-2 ring-coral-500/60' : '' }} {{ $isArchived ? 'opacity-90' : '' }}">
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
    @elseif (! $isArchived && ! $item->hasStandardUnit())
      <a href="{{ route('admin.inventory-items.edit', $item) }}" title="Counted in {{ $item->unit }}. Switch to ml, g or pcs." class="bg-stamp-50 text-stamp-600 hover:bg-stamp-100 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full shrink-0 transition-colors">Set unit</a>
    @endif
  </div>
  <p class="font-display font-bold text-3xl {{ $isLow ? 'text-coral-600' : 'text-stamp-700' }} mb-4">{{ $displayQty }} <span class="text-sm font-body font-bold text-stamp-300">{{ $item->unit }}</span></p>
  @if (! $isArchived && $item->stockInPurchaseUnit())
    <p class="-mt-3 mb-4 text-[11px] font-bold text-stamp-300">≈ {{ $item->stockInPurchaseUnit() }}</p>
  @endif
  @if ($isArchived)
    <div class="flex items-center gap-2">
      <span class="bg-cream-100 text-stamp-500 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full">Unused</span>
      <form method="POST" action="{{ route('admin.inventory-items.restore', $item->id) }}" class="flex-1">
        @csrf
        <button type="submit" class="w-full py-2.5 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors">Restore</button>
      </form>
    </div>
  @else
  @if ($item->batches->where('remaining_quantity', '>', 0)->isNotEmpty())
    <details class="mb-4 text-xs font-semibold text-stamp-500">
      <summary class="cursor-pointer font-extrabold">Stock batches &amp; expiry dates</summary>
      <ul class="mt-2 space-y-2">
        @foreach ($item->batches->where('remaining_quantity', '>', 0)->sortBy('expires_at') as $batch)
          <li class="{{ $batch->daysUntilExpiry() !== null && $batch->daysUntilExpiry() < 0 ? 'text-red-700' : ($batch->daysUntilExpiry() !== null && $batch->daysUntilExpiry() <= 7 ? 'text-amber-800' : 'text-stamp-500') }}">
            Batch #{{ $batch->id }} · {{ number_format($batch->remaining_quantity, 2) }} {{ $item->unit }}<br>
            {{ $batch->expires_at?->format('M j, Y') }} · {{ $batch->expiryLabel() }}
          </li>
        @endforeach
      </ul>
    </details>
  @endif
  <div class="flex gap-2">
    <a href="{{ route('admin.supply-purchases.index', ['restock' => $item->id]) }}" title="Buy more of this in Supply Purchases" class="flex-1 py-2.5 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      Restock
    </a>
    <button type="button" onclick="openStockOut({{ $item->id }}, {{ Illuminate\Support\Js::from($item->name) }}, {{ Illuminate\Support\Js::from($displayQty) }}, {{ Illuminate\Support\Js::from($item->unit) }})" class="flex-1 py-2.5 rounded-xl bg-coral-50 hover:bg-coral-500 hover:text-cream-50 text-coral-600 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-6-6m6 6l6-6"/></svg>
      Stock Out
    </button>
  </div>
  @php
    $usedBy = [];
    if ($item->recipeProductCount()) { $usedBy[] = 'used in '.$item->recipeProductCount().' product recipe(s)'; }
    if ($item->cup_sizes_count) { $usedBy[] = 'linked to '.$item->cup_sizes_count.' cup size(s)'; }
    $archiveWarning = 'Mark '.$item->name.' as unused? '.($usedBy ? ucfirst(implode(' and ', $usedBy)).'; sales will stop deducting this stock. ' : '').'Its history is kept and you can restore it anytime.';
  @endphp
  <div class="mt-2 flex gap-2">
    <a href="{{ route('admin.inventory-items.edit', $item) }}" class="flex-1 py-2 rounded-xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-[11px] text-center transition-colors">Edit</a>
    <form method="POST" action="{{ route('admin.inventory-items.destroy', $item) }}" onsubmit="return confirm({{ Illuminate\Support\Js::from($archiveWarning) }});" class="flex-1">
      @csrf
      @method('DELETE')
      <button type="submit" title="Mark as unused" class="w-full py-2 rounded-xl bg-cream-100 hover:bg-cream-200 text-stamp-500 font-extrabold text-[11px] transition-colors">Archive</button>
    </form>
  </div>
  @endif
</div>

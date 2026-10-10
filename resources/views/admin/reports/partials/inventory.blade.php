@php
  $kpi = $inventoryKpi;
@endphp

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
  @include('admin.reports.partials.kpi', ['href' => route('admin.inventory'), 'label' => 'Items tracked', 'value' => $kpi['items'], 'sub' => 'ingredients, cups and straws'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.inventory', ['stock' => 'low']), 'tone' => $kpi['low'] > 0 ? 'danger' : 'primary', 'label' => 'Low stock', 'value' => $kpi['low'], 'sub' => 'at or below reorder level'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.supply-purchases.index'), 'label' => 'Purchases', 'value' => $kpi['purchases'], 'sub' => 'recorded in this period'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.expenses.index'), 'label' => 'Spent on stock', 'value' => $peso($kpi['purchaseSpend']), 'sub' => 'logged in Expenses'])
  @include('admin.reports.partials.kpi', ['href' => route('admin.inventory-transactions.index'), 'label' => 'Stock-outs', 'value' => $kpi['stockOutMoves'], 'sub' => 'sales, waste and adjustments'])
</div>

<div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-6 overflow-x-auto">
  <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
    <div>
      <h3 class="font-display font-bold text-stamp-700 text-lg">Stock levels</h3>
      <p class="text-xs font-semibold text-stamp-300">On hand is as of today. Received, used and waste are for the chosen period.</p>
    </div>
    <a href="{{ route('admin.inventory') }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">Open Inventory &rarr;</a>
  </div>
  <table class="w-full min-w-[760px] text-left border-collapse">
    <thead>
      <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
        <th class="py-2 px-3">Item</th>
        <th class="py-2 px-3">On hand</th>
        <th class="py-2 px-3">Reorder at</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3 text-right">Received</th>
        <th class="py-2 px-3 text-right">Used by sales</th>
        <th class="py-2 px-3 text-right">Waste / adjusted</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($inventoryRows as $row)
        <tr class="border-t border-cream-200">
          <td class="py-3 px-3 text-sm font-bold text-stamp-700">{{ $row->item->name }} <span class="text-[10px] font-extrabold uppercase text-stamp-300">{{ $row->item->type === 'supply' ? 'Cups & straws' : 'Ingredient' }}</span></td>
          <td class="py-3 px-3 font-display font-bold text-sm {{ $row->isLow ? 'text-coral-600' : 'text-stamp-700' }}">{{ $qty($row->item->current_quantity) }} {{ $row->item->unit }}</td>
          <td class="py-3 px-3 text-sm font-semibold text-stamp-600">{{ $qty($row->item->reorder_level) }} {{ $row->item->unit }}</td>
          <td class="py-3 px-3">
            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $row->isLow ? 'bg-coral-50 text-coral-600' : 'bg-mint-50 text-mint-600' }}">{{ $row->isLow ? 'Low stock' : 'OK' }}</span>
          </td>
          <td class="py-3 px-3 text-right text-sm font-semibold text-stamp-600">{{ $row->received > 0 ? '+'.$qty($row->received) : '—' }}</td>
          <td class="py-3 px-3 text-right text-sm font-semibold text-stamp-600">{{ $row->sold > 0 ? '−'.$qty($row->sold) : '—' }}</td>
          <td class="py-3 px-3 text-right text-sm font-semibold text-stamp-600">{{ ($row->waste + $row->adjusted) > 0 ? '−'.$qty($row->waste + $row->adjusted) : '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="py-8 text-center text-sm font-semibold text-stamp-300">No inventory items yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="grid lg:grid-cols-2 gap-4">
  <div class="bg-white rounded-3xl shadow-soft p-5 md:p-6 overflow-x-auto">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display font-bold text-stamp-700">Purchases in this period</h3>
      <a href="{{ route('admin.supply-purchases.index') }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">View all &rarr;</a>
    </div>
    <table class="w-full min-w-[360px] text-left border-collapse">
      <tbody>
        @forelse ($periodPurchases as $purchase)
          <tr class="border-t border-cream-100 first:border-0">
            <td class="py-2.5 pr-3 text-xs font-semibold text-stamp-400 whitespace-nowrap">{{ $purchase->purchase_date->format('M j, Y') }}</td>
            <td class="py-2.5 pr-3 text-sm font-bold text-stamp-700"><a href="{{ route('admin.supply-purchases.show', $purchase) }}" class="hover:underline">{{ $purchase->supplier->supplier_name ?? $purchase->purchase_source ?? 'Unspecified' }}</a></td>
            <td class="py-2.5 text-right font-display font-bold text-sm text-stamp-700">{{ $peso($purchase->total_amount) }}</td>
          </tr>
        @empty
          <tr><td class="py-3 text-sm font-semibold text-stamp-300">No purchases in this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div id="stock-movements" class="bg-white rounded-3xl shadow-soft p-5 md:p-6 overflow-x-auto">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display font-bold text-stamp-700">Stock movements</h3>
      <a href="{{ route('admin.inventory-transactions.index') }}" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">View all &rarr;</a>
    </div>
    <form method="GET" action="{{ route('admin.reports.index') }}#stock-movements" class="flex items-center gap-2 mb-3 no-print">
      @foreach (request()->except(['movement_sort', 'movement_page', 'type']) as $key => $value)
        @if (is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
      @endforeach
      <input type="hidden" name="type" value="inventory">
      @include('admin.partials.sort-control', ['sortName' => 'movement_sort', 'sortOptions' => ['newest' => 'Newest first', 'oldest' => 'Oldest first']])
      <button class="rounded-xl bg-stamp-500 text-white px-3 py-2 text-xs font-bold">Apply</button>
    </form>
    <table class="w-full min-w-[360px] text-left border-collapse">
      <tbody>
        @forelse ($movementLog as $move)
          <tr class="border-t border-cream-100 first:border-0">
            <td class="py-2.5 pr-3 text-xs font-semibold text-stamp-400 whitespace-nowrap">{{ $move->inventory_transaction_date->format('M j, Y') }}</td>
            <td class="py-2.5 pr-3 text-sm font-bold text-stamp-700">{{ $move->inventoryItem->name ?? 'Unknown item' }}</td>
            <td class="py-2.5 pr-3"><span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full {{ $move->transaction_type === 'Restock' ? 'bg-mint-50 text-mint-600' : 'bg-coral-50 text-coral-600' }}">{{ $move->transaction_type }}</span></td>
            <td class="py-2.5 text-right font-display font-bold text-sm text-stamp-700 whitespace-nowrap">{{ $move->transaction_type === 'Restock' ? '+' : '−' }}{{ $qty($move->quantity) }} {{ $move->inventoryItem->unit ?? '' }}</td>
          </tr>
        @empty
          <tr><td class="py-3 text-sm font-semibold text-stamp-300">No stock movements in this period.</td></tr>
        @endforelse
      </tbody>
    </table>
    @include('admin.partials.pagination', ['paginator' => $movementLog])
  </div>
</div>

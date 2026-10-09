@if ($expiryAlerts->isNotEmpty())
  @php
    $expiredCount = $expiryAlerts->filter(fn ($batch) => $batch->daysUntilExpiry() < 0)->count();
    $soonCount = $expiryAlerts->count() - $expiredCount;
  @endphp
  <section id="expiry-alerts" aria-label="Ingredient expiry alerts" class="mb-6 rounded-3xl bg-white shadow-soft p-5 md:p-6">
    @if (! ($compact ?? false))
      <details class="group">
      <summary class="flex flex-wrap items-center justify-between gap-3 cursor-pointer rounded-xl focus-visible:ring-2 focus-visible:ring-stamp-300">
    @else
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
    @endif
      <h3 class="font-display font-bold text-xl text-stamp-700">Expiry alerts</h3>
      <div class="flex flex-wrap gap-2 text-xs font-extrabold">
        @if ($expiredCount)<span class="rounded-full bg-red-50 text-red-700 px-3 py-1">{{ $expiredCount }} expired {{ Str::plural('batch', $expiredCount) }}</span>@endif
        @if ($soonCount)<span class="rounded-full bg-amber-50 text-amber-800 px-3 py-1">{{ $soonCount }} {{ Str::plural('batch', $soonCount) }} expiring soon</span>@endif
        @if (! ($compact ?? false))
          <span class="flex items-center gap-2 text-stamp-500">
            <span class="group-open:hidden">View batches</span>
            <span class="hidden group-open:inline">Hide batches</span>
            <span aria-hidden="true" class="inline-block transition-transform group-open:rotate-180">▾</span>
          </span>
        @endif
      </div>
    @if (! ($compact ?? false))
      </summary>
      <div class="mt-4">
    @else
    </div>
    @endif
    <p class="text-xs font-semibold text-stamp-500 mb-4">Stock expiring within 7 days, including today. Use the earliest-expiring batch first. Expired stock is excluded from POS availability. Dates use Philippine time.</p>
    @if ($compact ?? false)
      <a href="{{ route('admin.inventory') }}#expiry-alerts" class="font-extrabold text-sm text-stamp-500 hover:underline">Review affected inventory &rarr;</a>
    @else
      <div class="space-y-3">
        @foreach ($expiryAlerts as $batch)
          <div class="rounded-2xl p-4 {{ $batch->daysUntilExpiry() < 0 ? 'bg-red-50 text-red-800' : 'bg-amber-50 text-amber-900' }}">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div>
                <p class="font-extrabold">{{ $batch->inventoryItem->name }} <span class="text-xs font-semibold">· Batch #{{ $batch->id }}</span></p>
                <p class="text-sm font-bold mt-1">{{ $batch->expiryLabel() }} · {{ $batch->expires_at->format('M j, Y') }}</p>
                <p class="text-xs font-semibold mt-1">Remaining: {{ number_format($batch->remaining_quantity, 2) }} {{ $batch->inventoryItem->unit }}
                  @if ($batch->supplyPurchase) · <a href="{{ route('admin.supply-purchases.show', $batch->supplyPurchase) }}" class="underline">Purchase {{ $batch->supplyPurchase->invoice_number ?: '#'.$batch->supply_purchase_id }}</a>@endif
                </p>
              </div>
              @if ($batch->daysUntilExpiry() < 0)
                <form method="POST" action="{{ route('admin.inventory-transactions.stock-out') }}" onsubmit="return confirm('Record this entire remaining batch as waste? Only confirm after removing it from physical stock.');">
                  @csrf
                  <input type="hidden" name="inventory_item_id" value="{{ $batch->inventory_item_id }}">
                  <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                  <input type="hidden" name="quantity" value="{{ $batch->remaining_quantity }}">
                  <input type="hidden" name="quantity_unit" value="base">
                  <input type="hidden" name="transaction_type" value="Waste">
                  <input type="hidden" name="inventory_transaction_date" value="{{ App\Models\InventoryBatch::expiryToday()->toDateString() }}">
                  <input type="hidden" name="reason" value="Expired batch #{{ $batch->id }}">
                  <button type="submit" class="rounded-xl bg-white px-3 py-2 text-xs font-extrabold shadow-soft-sm">Record expired batch as waste</button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
    @if (! ($compact ?? false))
      </div>
      </details>
    @endif
  </section>
@endif

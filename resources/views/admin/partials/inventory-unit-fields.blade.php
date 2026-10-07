{{--
  Fixed measurement units for an inventory item. Stock and recipes are always counted in ml, g or pcs;
  the unit the item is bought in (L, kg, bottle, box...) is converted to that automatically.
  Pass $item when editing. An item still on an old free-text unit (bottles, cans, kg...) gets a one-time
  "convert my current stock" step so nothing is lost.
--}}
@php
  $item = $item ?? null;
  $legacy = $item?->legacyConversion();
  $isLegacy = $item && ! $item->hasStandardUnit();

  $guessedPurchase = [
    'kg' => 'kg', 'kilogram' => 'kg', 'kilograms' => 'kg',
    'l' => 'L', 'liter' => 'L', 'liters' => 'L', 'litre' => 'L', 'litres' => 'L',
    'bottles' => 'bottle', 'cans' => 'can', 'packs' => 'pack', 'boxes' => 'box', 'sacks' => 'sack', 'cartons' => 'carton', 'cases' => 'case',
  ][mb_strtolower(trim((string) $item?->unit))] ?? null;

  $startUnit = old('unit', $item ? ($item->hasStandardUnit() ? $item->unit : ($legacy[0] ?? '')) : '');
  $startPurchase = old('secondary_unit', $item?->secondary_unit ?: ($isLegacy ? $guessedPurchase : null));
  $startFactor = old('conversion_factor', $item?->conversion_factor ? (float) $item->conversion_factor : null);
  $startConversion = old('unit_conversion', $isLegacy && $legacy ? $legacy[1] : null);
@endphp
<div id="unitFields" class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7"
     data-original-unit="{{ $item?->unit }}" data-original-qty="{{ $item?->current_quantity }}" data-original-reorder="{{ $item?->reorder_level }}">
  <h2 class="font-display font-bold text-stamp-700 text-lg mb-1">Units</h2>
  <p class="text-xs text-stamp-300 font-semibold mb-5">Stock and recipes are always counted in ml, g or pcs so amounts stay uniform. Pick how you buy it and we convert it for you.</p>

  @if ($isLegacy)
    <div class="mb-5 rounded-2xl bg-cream-100 px-4 py-3 text-xs font-bold text-stamp-600">
      This item is still counted in <span class="text-stamp-700">{{ $item->unit }}</span>. Choose its new stock unit below and your current stock, history and recipes are converted for you.
    </div>
  @endif

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Stock Unit</label>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
        <select name="unit" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          <option value="" @selected($startUnit === '')>Choose…</option>
          @foreach (App\Models\InventoryItem::BASE_UNITS as $code => $label)
            <option value="{{ $code }}" @selected($startUnit === $code)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div>
      <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Bought As <span class="text-stamp-300 normal-case font-semibold">(optional)</span></label>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
        <select name="secondary_unit" data-selected="{{ $startPurchase }}" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          <option value="">Not bought in bulk</option>
        </select>
      </div>
    </div>

    <div id="factorWrap" class="hidden col-span-2">
      <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1"><span id="factorLabel">1 bottle =</span></label>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3 flex items-center gap-2">
        <input type="number" min="0.0001" step="0.0001" name="conversion_factor" value="{{ $startFactor }}" placeholder="e.g. 750" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
        <span id="factorSuffix" class="text-xs font-extrabold text-stamp-400 shrink-0">ml</span>
      </div>
    </div>
  </div>
  <p id="unitHint" class="text-[11px] text-stamp-300 font-semibold mt-3"></p>

  @if ($item)
    <div id="conversionWrap" class="hidden mt-5 rounded-2xl border border-cream-200 bg-cream-50 p-4">
      <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1"><span id="conversionLabel">1 old = ? new</span></label>
      <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3 flex items-center gap-2">
        <input type="number" min="0.0001" step="0.0001" name="unit_conversion" value="{{ $startConversion }}" placeholder="e.g. 750" disabled class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
        <span id="conversionSuffix" class="text-xs font-extrabold text-stamp-400 shrink-0">ml</span>
      </div>
      <p id="conversionPreview" class="text-[11px] text-stamp-400 font-bold mt-2 ml-1"></p>
    </div>
  @endif
</div>
<script>
  (function () {
    var CATALOG = @json(App\Models\InventoryItem::PURCHASE_UNITS);
    var ALIASES = { kilogram: 'kg', kilograms: 'kg', liter: 'l', liters: 'l', litre: 'l', litres: 'l', gallons: 'gallon', bottles: 'bottle', cans: 'can', packs: 'pack', boxes: 'box', sacks: 'sack', cartons: 'carton', cases: 'case', pieces: 'pcs', piece: 'pcs', pc: 'pcs' };
    var root = document.getElementById('unitFields');
    var original = (root.dataset.originalUnit || '').trim();
    var originalQty = parseFloat(root.dataset.originalQty) || 0;
    var originalReorder = parseFloat(root.dataset.originalReorder) || 0;
    var baseSel = root.querySelector('[name=unit]');
    var buySel = root.querySelector('[name=secondary_unit]');
    var factorWrap = document.getElementById('factorWrap');
    var factorInput = root.querySelector('[name=conversion_factor]');
    var convWrap = document.getElementById('conversionWrap');
    var convInput = root.querySelector('[name=unit_conversion]');

    function canon(u) { u = String(u || '').trim().toLowerCase(); return ALIASES[u] || u; }
    function num(n) { return Number(n).toLocaleString(undefined, { maximumFractionDigits: 2 }); }

    function fillPurchaseOptions() {
      var base = baseSel.value;
      var keep = buySel.value || buySel.dataset.selected || '';
      buySel.innerHTML = '<option value="">Not bought in bulk</option>';
      Object.keys(CATALOG[base] || {}).forEach(function (u) {
        var size = CATALOG[base][u];
        buySel.insertAdjacentHTML('beforeend', '<option value="' + u + '">' + u + (size ? ' (= ' + num(size) + ' ' + base + ')' : '') + '</option>');
      });
      buySel.value = Array.prototype.some.call(buySel.options, function (o) { return o.value === keep; }) ? keep : '';
      if (base) { buySel.dataset.selected = ''; }
    }

    function refresh() {
      var base = baseSel.value;
      var buy = buySel.value;
      var size = buy && CATALOG[base] ? CATALOG[base][buy] : null;
      var needsSize = !!buy && (size === null || size === undefined);

      factorWrap.classList.toggle('hidden', !needsSize);
      factorInput.required = needsSize;
      document.getElementById('factorLabel').textContent = '1 ' + buy + ' =';
      document.getElementById('factorSuffix').textContent = base;

      var hint = '';
      if (!base) { hint = 'Choose the unit this item is counted in.'; }
      else if (!buy) { hint = 'Counted and bought in ' + base + '.'; }
      else if (!needsSize) { hint = '1 ' + buy + ' = ' + num(size) + ' ' + base + ', converted automatically. Receiving 2 ' + buy + ' adds ' + num(size * 2) + ' ' + base + '.'; }
      else { hint = 'Stock is counted in ' + base + '. Receiving a ' + buy + ' adds the amount you enter above.'; }
      document.getElementById('unitHint').textContent = hint;

      if (!convWrap) { return; }

      var changed = !!original && !!base && base !== original;
      var buyIsOld = changed && !!buy && canon(buy) === canon(original);
      var factor = needsSize ? parseFloat(factorInput.value) : size;

      convInput.disabled = !changed;
      if (buyIsOld && factor > 0) { convInput.value = factor; }
      convWrap.classList.toggle('hidden', !changed || buyIsOld);
      convInput.required = changed && !buyIsOld;
      document.getElementById('conversionLabel').textContent = '1 ' + original + ' =';
      document.getElementById('conversionSuffix').textContent = base;

      var conv = parseFloat(convInput.value);
      var preview = '';
      if (changed && conv > 0) { preview = 'Current stock ' + num(originalQty) + ' ' + original + ' becomes ' + num(originalQty * conv) + ' ' + base + '.'; }
      document.getElementById('conversionPreview').textContent = preview;
      if (buyIsOld && preview) { document.getElementById('unitHint').textContent += ' ' + preview; }

      var reorder = document.querySelector('[name=reorder_level]');
      if (reorder && original) {
        if (changed && conv > 0 && !reorder.dataset.touched) {
          reorder.value = Math.round(originalReorder * conv * 100) / 100;
          reorder.dataset.auto = '1';
        } else if (!changed && reorder.dataset.auto) {
          reorder.value = originalReorder;
          delete reorder.dataset.auto;
        }
      }
    }

    // the reorder field sits below this card, so listen at the document level
    document.addEventListener('input', function (e) { if (e.target.name === 'reorder_level') { e.target.dataset.touched = '1'; } });
    baseSel.addEventListener('change', function () { fillPurchaseOptions(); refresh(); });
    [buySel, factorInput, convInput].forEach(function (el) { if (el) { el.addEventListener('input', refresh); el.addEventListener('change', refresh); } });

    fillPurchaseOptions();
    refresh();
    // run again once the whole form exists, so the reorder field below this card is converted too
    document.addEventListener('DOMContentLoaded', refresh);
  })();
</script>

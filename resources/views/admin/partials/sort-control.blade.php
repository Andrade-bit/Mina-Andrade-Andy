<div class="flex items-center gap-2 rounded-2xl bg-cream-100 px-3 py-2 text-sm font-semibold text-stamp-600">
  <label for="{{ $sortName ?? 'sort' }}">Sort by</label>
  <select id="{{ $sortName ?? 'sort' }}" name="{{ $sortName ?? 'sort' }}" class="bg-transparent outline-none max-w-[160px]">
    @foreach ($sortOptions ?? ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'highest' => 'Highest amount', 'lowest' => 'Lowest amount'] as $sortKey => $sortLabel)
      <option value="{{ $sortKey }}" @selected(request($sortName ?? 'sort', $sortDefault ?? 'newest') === $sortKey)>{{ $sortLabel }}</option>
    @endforeach
  </select>
</div>

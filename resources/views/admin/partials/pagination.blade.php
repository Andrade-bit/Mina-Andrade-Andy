@if ($paginator->hasPages())
  <div class="flex items-center justify-between flex-wrap gap-3 pt-4">
    <p class="text-xs font-semibold text-stamp-300">
      Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </p>
    <div class="flex items-center gap-2">
      @if ($paginator->onFirstPage())
        <span class="px-4 py-2 rounded-xl bg-cream-100 text-stamp-300 text-xs font-bold cursor-not-allowed">Prev</span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 text-xs font-bold transition-colors">Prev</a>
      @endif
      <span class="text-xs font-bold text-stamp-500 px-2">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-cream-100 hover:bg-stamp-100 text-stamp-600 text-xs font-bold transition-colors">Next</a>
      @else
        <span class="px-4 py-2 rounded-xl bg-cream-100 text-stamp-300 text-xs font-bold cursor-not-allowed">Next</span>
      @endif
    </div>
  </div>
@endif

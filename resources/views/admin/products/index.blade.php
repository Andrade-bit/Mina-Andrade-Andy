<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Products</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          display: ['"Baloo 2"', 'sans-serif'],
          body: ['"Nunito"', 'sans-serif'],
        },
        colors: {
          cream: { 50: '#FFFDF9', 100: '#FBF3E4', 200: '#F1E6CC' },
          stamp: { 50: '#EAF3FA', 100: '#D3E6F3', 300: '#8FBBDD', 500: '#2F6690', 600: '#24506F', 700: '#1A3B52' },
          mint: { 50: '#EAF5EF', 500: '#6FAE8B', 600: '#3F7457' },
          coral: { 50: '#FBEDEB', 500: '#E0776B', 600: '#9C4A41' },
        },
        boxShadow: {
          soft: '0 16px 32px -10px rgba(26,59,82,0.16), 0 4px 10px rgba(26,59,82,0.06)',
          'soft-sm': '0 6px 14px -4px rgba(26,59,82,0.15)',
          'soft-inset': 'inset 0 2px 6px rgba(26,59,82,0.14), inset 0 -1px 1px rgba(255,255,255,0.7)',
          'soft-btn': '0 5px 0 #1A3B52, 0 10px 18px rgba(47,102,144,0.3)',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex">

  @include('admin.partials.sidebar', ['active' => 'products'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Products</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Your Catbrews menu, organized by category</p>
      </div>
      <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Product
      </a>
    </div>


    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
      <div class="flex items-center gap-2 bg-white rounded-2xl shadow-soft-sm px-4 py-2.5 flex-1 min-w-[220px]">
        <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full bg-transparent outline-none text-sm text-stamp-700 placeholder-stamp-300 font-semibold">
      </div>
      <div class="bg-white rounded-2xl shadow-soft-sm px-4 py-2.5">
        <select name="category" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
          <option value="">All Categories</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->category_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="bg-white rounded-2xl shadow-soft-sm px-4 py-2.5">
        <select name="performance" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
          <option value="">All Performance</option>
          <option value="top" @selected(request('performance') === 'top')>Top Sellers (30d)</option>
          <option value="slow" @selected(request('performance') === 'slow')>Slow Movers (30d)</option>
          <option value="never" @selected(request('performance') === 'never')>Never Sold</option>
        </select>
      </div>
      <div class="bg-white rounded-2xl shadow-soft-sm px-4 py-2.5">
        <select name="availability" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
          <option value="">All Availability</option>
          <option value="unavailable" @selected(request('availability') === 'unavailable')>Not available in POS</option>
        </select>
      </div>
      <label class="flex items-center gap-2 bg-white rounded-2xl shadow-soft-sm px-4 py-2.5 text-sm text-stamp-600 font-semibold cursor-pointer">
        <input type="checkbox" name="archived" value="1" @checked(request()->boolean('archived')) class="w-4 h-4 accent-stamp-500">
        Archived
      </label>
      @include('admin.partials.sort-control', ['sortDefault' => 'name_asc', 'sortOptions' => ['name_asc' => 'Name A–Z', 'name_desc' => 'Name Z–A', 'newest' => 'Newest first', 'oldest' => 'Oldest first']])
      <button type="submit" class="px-5 py-2.5 rounded-2xl bg-stamp-500 hover:bg-stamp-600 text-cream-50 font-extrabold text-xs transition-colors">Search</button>
      @if (request('search') || request('category') || request('performance') || request('availability') || request()->boolean('archived') || request()->filled('sort'))
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-500 font-extrabold text-xs transition-colors">Clear</a>
      @endif
    </form>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="grid gap-3">
        @forelse ($products as $product)
          <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap">
            <div class="w-11 h-11 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 shrink-0 overflow-hidden">
              @if ($product->image)
                <img src="{{ $product->imageUrl() }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover">
              @else
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              @endif
            </div>
            <div class="flex-1 min-w-[160px]">
              <p class="font-extrabold text-stamp-700">{{ $product->product_name }}
                @if (! $product->trashed() && $unavailable->has($product->id))
                  <span class="ml-1.5 align-middle bg-coral-50 text-coral-600 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full">Not available in POS</span>
                @endif
              </p>
              <p class="text-xs text-stamp-400 font-semibold">{{ $product->productCategory->category_name ?? 'Uncategorized' }}</p>
              @if (! $product->trashed() && $unavailable->has($product->id))
                <p class="mt-1 text-[11px] font-bold text-coral-600">Can't be sold now: out of {{ $unavailable[$product->id]->pluck('name')->join(', ', ' and ') }}. <a href="{{ route('admin.supply-purchases.index', ['restock' => $unavailable[$product->id]->first()->id]) }}" class="underline">Restock</a></p>
              @endif
            </div>
            @if (request('performance') === 'never')
              <span class="bg-coral-50 text-coral-600 text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full shrink-0">Never Sold</span>
            @elseif (request('performance'))
              <span class="bg-{{ request('performance') === 'top' ? 'mint' : 'coral' }}-50 text-{{ request('performance') === 'top' ? 'mint-600' : 'coral-500' }} text-[10px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-full shrink-0">{{ $soldCounts[$product->id] ?? 0 }} sold (30d)</span>
            @endif
            <div class="flex items-center gap-2">
              @if ($product->trashed())
                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                  @csrf
                  <button type="submit" class="px-3.5 py-2 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors">Restore</button>
                </form>
              @else
                <a href="{{ route('admin.products.edit', $product) }}" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Archive {{ $product->product_name }}? {{ (int) $product->sold_today > 0 ? 'It was ordered '.(int) $product->sold_today.' time(s) today, so a customer may be ordering it right now. ' : ((int) $product->sold_total > 0 ? 'It has been ordered '.(int) $product->sold_total.' time(s) in total. ' : 'It has never been ordered. ') }}It will leave the menu and POS, but past sales keep it. You can restore it anytime.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" title="Archive" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">
            @if (request('search') || request('category'))
              No products match your filters.
            @else
              No products yet. Click "Add Product" to build your menu.
            @endif
          </p>
        @endforelse
      </div>
      @include('admin.partials.pagination', ['paginator' => $products])
    </div>
  </main>

  @include('admin.partials.toasts')

</body>
</html>

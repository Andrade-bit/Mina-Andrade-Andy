<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Add Product</title>
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
<body class="min-h-screen bg-cream-50 font-body text-stamp-700">

  <header class="bg-white shadow-soft-sm px-5 md:px-10 py-4 flex items-center justify-between flex-wrap gap-3 sticky top-0 z-20">
    <div>
      <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Products &rsaquo; Add New</p>
      <h1 class="font-display font-bold text-xl md:text-2xl text-stamp-700">Add Product</h1>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.products.index') }}" class="px-4 md:px-5 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
      <button type="submit" form="productForm" class="px-5 md:px-6 py-2.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Product</button>
    </div>
  </header>

  <main class="max-w-4xl mx-auto p-5 md:p-8">


    <form id="productForm" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-5">Basic Information</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Product Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="text" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. Hazelnut Latte" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Category</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <select name="product_category_id" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                <option value="" disabled @selected(! old('product_category_id'))>Choose a category</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" @selected((int) old('product_category_id') === $category->id)>{{ $category->category_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-5">Product Photo</h2>
        <div class="flex items-center gap-5 flex-wrap">
          <div id="photoPreviewWrap" class="w-28 h-28 rounded-2xl bg-cream-100 border-2 border-dashed border-stamp-100 flex items-center justify-center text-stamp-300 shrink-0 overflow-hidden">
            <svg id="photoPlaceholderIcon" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 16.5zm3-9.75h.008v.008H6V6.75zm2.47 4.03a2.25 2.25 0 113.182 0l-4.773 4.772a1.5 1.5 0 01-2.121 0L3 14.69M15 12l3.586-3.586a1.5 1.5 0 012.121 0L21 9"/></svg>
            <img id="photoPreviewImg" src="" alt="" class="hidden w-full h-full object-cover">
          </div>
          <div class="flex-1 min-w-[200px]">
            <label for="photoInput" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm cursor-pointer transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Choose Photo
            </label>
            <input id="photoInput" type="file" name="image" accept="image/*" class="hidden" onchange="previewProductPhoto(this)">
            <p class="text-[11px] text-stamp-300 font-semibold mt-2">JPG or PNG, up to 4MB. Optional — a placeholder is used if left blank.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-1">Sizes &amp; Pricing</h2>
        <p class="text-xs text-stamp-300 font-semibold mb-5">Leave price blank to use the size's default. Uncheck a size to hide it for this product.</p>
        <div class="space-y-2">
          @foreach ($cupSizes as $cupSize)
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3 flex items-center gap-3">
              <label class="flex items-center gap-2 flex-1 min-w-0">
                <input type="checkbox" name="sizes[{{ $cupSize->id }}][is_available]" value="1" @checked(old("sizes.{$cupSize->id}.is_available", true)) class="w-4 h-4 accent-stamp-500 shrink-0">
                <span class="text-sm font-bold text-stamp-700 truncate">{{ $cupSize->size_name }}</span>
              </label>
              <span class="text-xs text-stamp-300 font-semibold shrink-0">Default &#8369;{{ number_format($cupSize->price, 2) }}</span>
              <div class="flex items-center gap-1 shrink-0">
                <span class="text-stamp-400 text-xs font-bold">&#8369;</span>
                <input type="number" min="0" step="0.01" name="sizes[{{ $cupSize->id }}][price]" value="{{ old('sizes.'.$cupSize->id.'.price') }}" placeholder="{{ number_format($cupSize->price, 2) }}" class="w-20 bg-white rounded-xl px-2 py-1.5 text-xs font-bold text-stamp-700 outline-none">
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-1">Ingredients Used <span class="ml-1 align-middle text-[10px] font-extrabold uppercase tracking-wide text-coral-600 bg-coral-50 px-2 py-0.5 rounded-full">Required</span></h2>
        <p class="text-xs text-stamp-300 font-semibold mb-5">Type how much of each ingredient one drink uses in each cup size, in the ingredient's stock unit (ml, g or pcs). For example Caramel Syrup: Small 20, Medium 30, Large 40. Leave an ingredient blank if this drink doesn't use it. When stock is below a size's amount, the POS marks that size Not available.</p>
        @php $recipeSize = $cupSizes->firstWhere('is_recipe_size', true); @endphp
        <p class="-mt-3 mb-5 text-xs font-bold text-stamp-400">
          @if ($recipeSize)
            A size left blank uses the {{ $recipeSize->size_name }} amount, adjusted for cup volume.
          @else
            A size left blank uses the smallest amount typed.
          @endif
        </p>
        <p id="ingredientHint" class="mb-4 rounded-xl bg-coral-50 px-3 py-2 text-xs font-bold text-coral-600">Enter an amount for at least one ingredient to save this product.</p>
        @if ($inventoryItems->isEmpty())
          <p class="text-sm font-semibold text-stamp-300">No ingredients yet. <a href="{{ route('admin.supply-purchases.index', ['record' => 1]) }}" class="text-stamp-500 underline">Record a purchase</a> to add some first.</p>
        @else
          <div class="overflow-x-auto -mx-1 px-1 pb-1">
            <div class="min-w-[460px] space-y-2">
              <div class="flex items-center gap-2 px-4 text-[10px] font-extrabold uppercase tracking-wide text-stamp-300">
                <span class="flex-1">Ingredient</span>
                @foreach ($cupSizes as $size)
                  <span class="w-16 text-center truncate" title="{{ $size->size_name }}">{{ $size->size_name }}</span>
                @endforeach
                <span class="w-10"></span>
              </div>
              @foreach ($inventoryItems as $item)
                <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3 flex items-center gap-2">
                  <span class="text-sm font-bold text-stamp-700 flex-1 min-w-0 truncate">{{ $item->name }}</span>
                  @foreach ($cupSizes as $size)
                    <input type="number" min="0" step="0.01" name="ingredients[{{ $item->id }}][{{ $size->id }}]" value="{{ old('ingredients.'.$item->id.'.'.$size->id, $sizeAmounts[$item->id][$size->id] ?? null) }}" placeholder="0" aria-label="{{ $item->name }}, {{ $size->size_name }}" class="w-16 bg-white rounded-xl px-2 py-1.5 text-xs font-bold text-stamp-700 outline-none text-right">
                  @endforeach
                  @if ($item->hasStandardUnit())
                    <span class="text-xs font-extrabold text-stamp-500 w-10 shrink-0">{{ $item->unit }}</span>
                  @else
                    <a href="{{ route('admin.inventory-items.edit', $item) }}" title="This item is counted in {{ $item->unit }}. Set it to ml, g or pcs first." class="text-[10px] font-extrabold uppercase text-coral-600 bg-coral-50 rounded-full px-2 py-1 shrink-0 w-10 text-center">Set unit</a>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <div class="flex items-center justify-end gap-2 pb-6">
        <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Product</button>
      </div>
    </form>
  </main>

  <script>
    function previewProductPhoto(input){
      if (!input.files || !input.files[0]) return;
      const img = document.getElementById('photoPreviewImg');
      const icon = document.getElementById('photoPlaceholderIcon');
      img.src = URL.createObjectURL(input.files[0]);
      img.classList.remove('hidden');
      icon.classList.add('hidden');
    }
  </script>

  <script>
    // A product needs at least one ingredient: saving stays off until one has an amount.
    (function () {
      var saveButtons = document.querySelectorAll('button[type=submit][form=productForm], #productForm button[type=submit]');
      var hint = document.getElementById('ingredientHint');
      var amounts = document.querySelectorAll('input[name^="ingredients["]');

      function refresh() {
        var ready = Array.prototype.some.call(amounts, function (input) { return parseFloat(input.value) > 0; });
        saveButtons.forEach(function (button) {
          button.disabled = !ready;
          button.classList.toggle('opacity-50', !ready);
          button.classList.toggle('cursor-not-allowed', !ready);
          button.title = ready ? '' : 'Add at least one ingredient to save';
        });
        if (hint) { hint.classList.toggle('hidden', ready); }
      }

      amounts.forEach(function (input) { input.addEventListener('input', refresh); });
      refresh();
    })();
  </script>

  @include('admin.partials.card-strokes')
  @include('admin.partials.toasts')

</body>
</html>

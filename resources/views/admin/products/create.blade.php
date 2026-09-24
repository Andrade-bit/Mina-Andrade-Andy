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
          soft: '0 16px 32px -10px rgba(26,59,82,0.18), 0 4px 10px rgba(26,59,82,0.08)',
          'soft-inset': 'inset 0 2px 6px rgba(26,59,82,0.14), inset 0 -1px 1px rgba(255,255,255,0.7)',
          'soft-btn': '0 5px 0 #1A3B52, 0 10px 18px rgba(47,102,144,0.35)',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex items-center justify-center p-6 relative overflow-hidden">

  <div class="absolute -top-28 -left-24 w-80 h-80 bg-stamp-100 rounded-full blur-3xl opacity-70"></div>
  <div class="absolute -bottom-28 -right-20 w-96 h-96 bg-mint-500/10 rounded-full blur-3xl"></div>

  <a href="{{ route('admin.products.index') }}" class="absolute top-6 left-6 z-10 w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 shadow-soft-sm flex items-center justify-center text-stamp-500 transition-colors" title="Back to Products">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
  </a>

  <div class="relative w-full max-w-md bg-cream-50 rounded-[2rem] shadow-soft p-10">
    <div class="text-center mb-7">
      <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      </div>
      <h1 class="font-display font-bold text-2xl text-stamp-700">Add Product</h1>
      <p class="text-xs text-stamp-400 font-semibold mt-1">Add a new item to the Catbrews menu</p>
    </div>

    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-5">
      @csrf
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-2 ml-1">Product Name</label>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3.5">
          <input type="text" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. Hazelnut Latte" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
        </div>
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-2 ml-1">Category</label>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3.5">
          <select name="product_category_id" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            <option value="" disabled selected>Choose a category</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" @selected((int) old('product_category_id') === $category->id)>{{ $category->category_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <button type="submit" class="block w-full text-center bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold tracking-wide text-sm py-4 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
        Add to Menu
      </button>
    </form>
  </div>

</body>
</html>

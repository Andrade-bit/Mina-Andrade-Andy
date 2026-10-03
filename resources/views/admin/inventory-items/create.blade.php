<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Add Inventory Item</title>
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
      <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Inventory &rsaquo; Add New</p>
      <h1 class="font-display font-bold text-xl md:text-2xl text-stamp-700">Add Inventory Item</h1>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.inventory-items.index') }}" class="px-4 md:px-5 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
      <button type="submit" form="itemForm" class="px-5 md:px-6 py-2.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Item</button>
    </div>
  </header>

  <main class="max-w-3xl mx-auto p-5 md:p-8">

    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <form id="itemForm" method="POST" action="{{ route('admin.inventory-items.store') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-5">Item Details</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Item Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Vanilla Syrup" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Photo <span class="text-stamp-300 normal-case font-semibold">(optional, helps tell look-alike items apart)</span></label>
            <div class="flex items-center gap-4">
              <div id="photoPreviewWrap" class="w-16 h-16 rounded-2xl bg-cream-100 border-2 border-dashed border-stamp-100 flex items-center justify-center text-stamp-300 shrink-0 overflow-hidden">
                <svg id="photoPlaceholderIcon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 16.5zm3-9.75h.008v.008H6V6.75zm2.47 4.03a2.25 2.25 0 113.182 0l-4.773 4.772a1.5 1.5 0 01-2.121 0L3 14.69M15 12l3.586-3.586a1.5 1.5 0 012.121 0L21 9"/></svg>
                <img id="photoPreviewImg" src="" alt="" class="hidden w-full h-full object-cover">
              </div>
              <label for="photoInput" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-xs cursor-pointer transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Choose Photo
              </label>
              <input id="photoInput" type="file" name="image" accept="image/*" class="hidden" onchange="previewItemPhoto(this)">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Category</label>
              <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
                <select name="type" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                  <option value="ingredient" @selected(old('type', 'ingredient') === 'ingredient')>Procurement</option>
                  <option value="supply" @selected(old('type') === 'supply')>Supplier (Cups/Straws)</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Base Unit</label>
              <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
                <input type="text" name="unit" value="{{ old('unit') }}" placeholder="e.g. ml, g, pcs" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-1">Stock Tracking</h2>
        <p class="text-xs text-stamp-300 font-semibold mb-5">Set the starting stock, reorder threshold, and optional bulk purchase unit.</p>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Starting Quantity</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="number" min="0" step="0.01" name="current_quantity" value="{{ old('current_quantity', 0) }}" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reorder Level</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="number" min="0" step="0.01" name="reorder_level" value="{{ old('reorder_level', 0) }}" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Bulk Unit <span class="text-stamp-300 normal-case font-semibold">(optional)</span></label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="text" name="secondary_unit" value="{{ old('secondary_unit') }}" placeholder="e.g. liters, box" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">1 Bulk Unit =</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="number" min="0.0001" step="0.0001" name="conversion_factor" value="{{ old('conversion_factor') }}" placeholder="1000" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
        </div>
        <p class="text-[11px] text-stamp-300 font-semibold mt-3">e.g. Bulk Unit "liters", conversion "1000" means 1 liter = 1000 ml (base unit above).</p>
      </div>

      <div class="flex items-center justify-end gap-2 pb-6">
        <a href="{{ route('admin.inventory-items.index') }}" class="px-5 py-3 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Item</button>
      </div>
    </form>
  </main>

  <script>
    function previewItemPhoto(input){
      if (!input.files || !input.files[0]) return;
      const img = document.getElementById('photoPreviewImg');
      const icon = document.getElementById('photoPlaceholderIcon');
      img.src = URL.createObjectURL(input.files[0]);
      img.classList.remove('hidden');
      icon.classList.add('hidden');
    }
  </script>

</body>
</html>

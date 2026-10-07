<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Cup Sizes</title>
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

  @include('admin.partials.sidebar', ['active' => 'cup-sizes'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Cup Sizes</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Pricing tiers used across the menu (Small / Medium / Large)</p>
      </div>
      <button onclick="openCreate()" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Cup Size
      </button>
    </div>

    <div class="flex justify-end mb-3">
      @if (request()->boolean('archived'))
        <a href="{{ route('admin.cup-sizes.index') }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">&larr; Back to active</a>
      @else
        <a href="{{ route('admin.cup-sizes.index', ['archived' => 1]) }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">View archived</a>
      @endif
    </div>


    <div class="mb-5 rounded-2xl bg-stamp-50 px-4 py-3 text-xs font-bold text-stamp-600">
      How much of each ingredient a size needs is set per product: open a product from <a href="{{ route('admin.products.index') }}" class="underline">Products</a> and fill in its Ingredients Used grid (for example Caramel Syrup: Small 1, Medium 2, Large 3). When a product leaves a size blank, it uses the recipe size's amount adjusted by cup volume, so set each size's volume in ml and mark the recipe size here as that fallback.
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="grid gap-3">
        @forelse ($cupSizes as $size)
          <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap">
            <div class="w-11 h-11 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 shrink-0">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-6 0v4.5L4.5 15A2 2 0 006.28 18h11.44a2 2 0 001.78-3L15 7.5V3"/></svg>
            </div>
            <div class="flex-1 min-w-[160px]">
              <p class="font-extrabold text-stamp-700">{{ $size->size_name }}</p>
              <p class="text-xs text-stamp-400 font-semibold">{{ $size->inventoryItem->name ?? 'Cups not tracked in stock' }}@if ($size->volume_ml) &middot; {{ rtrim(rtrim(number_format($size->volume_ml, 2), '0'), '.') }} ml @endif</p>
              @if ($size->is_recipe_size)
                <span class="inline-block mt-1 bg-mint-50 text-mint-600 text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full">Recipe size</span>
              @endif
            </div>
            <span class="bg-cream-100 text-stamp-600 text-xs font-extrabold px-3 py-1.5 rounded-full">₱{{ number_format($size->price, 2) }}</span>
            <div class="flex items-center gap-2">
              @unless ($size->trashed())
              <button type="button" onclick='openEditModal(@json($size))' class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
              @endunless
              @if ($size->trashed())
                <form method="POST" action="{{ route('admin.cup-sizes.restore', $size->id) }}">
                  @csrf
                  <button type="submit" class="px-3.5 py-2 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors">Restore</button>
                </form>
              @else
              <form method="POST" action="{{ route('admin.cup-sizes.destroy', $size) }}" onsubmit="return confirm('Archive {{ $size->size_name }}? {{ $size->sales_transaction_items_count ? 'Used in '.$size->sales_transaction_items_count.' sale line(s). ' : '' }}You can restore it anytime.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Archive" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></button>
              </form>
              @endif
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">No cup sizes yet. Click "Add Cup Size" to create one.</p>
        @endforelse
      </div>
    </div>
  </main>

  <!-- Create Cup Size Modal -->
  <div id="createModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-sm p-7 relative">
      <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-6 0v4.5L4.5 15A2 2 0 006.28 18h11.44a2 2 0 001.78-3L15 7.5V3"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Add Cup Size</h3>
      </div>
      <form method="POST" action="{{ route('admin.cup-sizes.store') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Size Name</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="size_name" value="{{ old('size_name') }}" placeholder="e.g. Extra Large" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Price (₱)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="number" min="0" step="0.01" name="price" value="{{ old('price') }}" placeholder="0.00" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Volume (ml) <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="number" min="1" max="5000" step="1" name="volume_ml" value="{{ old('volume_ml') }}" placeholder="e.g. 480" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
          <label class="flex items-start gap-2 mt-2 ml-1 text-[11px] font-semibold text-stamp-400 cursor-pointer">
            <input type="checkbox" name="is_recipe_size" value="1" @checked(old('is_recipe_size')) class="mt-0.5 rounded">
            <span>Fallback size: a size left blank on a product uses this size's amount, adjusted by cup volume.</span>
          </label>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Cup Stock Used <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="inventory_item_id" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="">None (don't track cups)</option>
              @foreach ($inventoryItems as $item)
                <option value="{{ $item->id }}" @selected((int) old('inventory_item_id') === $item->id)>{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">Pick the inventory item for this cup, e.g. Small Cups (12oz). Every sale of this size takes 1 off that stock automatically.</p>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Create Cup Size
        </button>
      </form>
    </div>
  </div>

  <!-- Edit Cup Size Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-sm p-7 relative">
      <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Edit Cup Size</h3>
      </div>
      <form id="editForm" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Size Name</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="size_name" id="edit_size_name" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Price (₱)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="number" min="0" step="0.01" name="price" id="edit_price" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Volume (ml) <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="number" min="1" max="5000" step="1" name="volume_ml" id="edit_volume_ml"  placeholder="e.g. 480" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
          <label class="flex items-start gap-2 mt-2 ml-1 text-[11px] font-semibold text-stamp-400 cursor-pointer">
            <input type="checkbox" name="is_recipe_size" value="1" id="edit_is_recipe_size"  class="mt-0.5 rounded">
            <span>Fallback size: a size left blank on a product uses this size's amount, adjusted by cup volume.</span>
          </label>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Cup Stock Used <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="inventory_item_id" id="edit_inventory_item_id" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="">None (don't track cups)</option>
              @foreach ($inventoryItems as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">Pick the inventory item for this cup, e.g. Small Cups (12oz). Every sale of this size takes 1 off that stock automatically.</p>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Save Changes
        </button>
      </form>
    </div>
  </div>

  <script>
    function openCreate(){ document.getElementById('createModal').classList.remove('hidden'); }

    function openEditModal(size){
      document.getElementById('editForm').action = "{{ route('admin.cup-sizes.update', ':id') }}".replace(':id', size.id);
      document.getElementById('edit_size_name').value = size.size_name || '';
      document.getElementById('edit_price').value = size.price || '';
      document.getElementById('edit_inventory_item_id').value = size.inventory_item_id || '';
      document.getElementById('edit_volume_ml').value = size.volume_ml ? parseFloat(size.volume_ml) : '';
      document.getElementById('edit_is_recipe_size').checked = !!size.is_recipe_size;
      document.getElementById('editModal').classList.remove('hidden');
    }

    @if ($errors->any())
      openCreate();
    @endif
  </script>

  @include('admin.partials.toasts')

</body>
</html>

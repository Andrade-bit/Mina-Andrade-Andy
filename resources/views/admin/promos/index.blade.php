<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Promos</title>
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

  @include('admin.partials.sidebar', ['active' => 'promos'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Promos</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Real promo codes &mdash; validated and saved on the sale at checkout</p>
      </div>
      <button onclick="openCreate()" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Promo
      </button>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="grid gap-3">
        @forelse ($promos as $promo)
          @php
            $expired = $promo->expires_at && $promo->expires_at->isPast();
          @endphp
          <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap {{ ! $promo->active || $expired ? 'opacity-50' : '' }}">
            <div class="w-11 h-11 rounded-full {{ $promo->type === 'percent' ? 'bg-mint-50 text-mint-600' : 'bg-stamp-50 text-stamp-500' }} flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-5.586 5.586a1 1 0 01-1.414 0L6.293 10.293A1 1 0 016 9.586V4a1 1 0 011-1z"/></svg>
            </div>
            <div class="flex-1 min-w-[160px]">
              <p class="font-extrabold text-stamp-700">{{ $promo->code }}</p>
              <p class="text-xs text-stamp-400 font-semibold">
                {{ $promo->type === 'percent' ? number_format($promo->value, 0).'% off' : '₱'.number_format($promo->value, 2).' off' }}
                &middot; {{ $promo->expires_at ? 'expires '.$promo->expires_at->format('M j, Y') : 'no expiry' }}
              </p>
              @if ($promo->reason)
                <p class="text-[11px] text-stamp-300 font-semibold italic truncate">{{ $promo->reason }}</p>
              @endif
            </div>
            <span class="text-[11px] font-extrabold px-3 py-1.5 rounded-full {{ $expired ? 'bg-coral-50 text-coral-600' : ($promo->active ? 'bg-mint-50 text-mint-600' : 'bg-cream-100 text-stamp-400') }}">
              {{ $expired ? 'Expired' : ($promo->active ? 'Active' : 'Inactive') }}
            </span>
            <div class="flex items-center gap-2">
              <button type="button" onclick='openEditModal(@json($promo))' class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
              <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Remove promo {{ $promo->code }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">No promos yet. Click "Add Promo" to create one.</p>
        @endforelse
      </div>
    </div>
  </main>

  <!-- Create Promo Modal -->
  <div id="createModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-sm p-7 relative">
      <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Add Promo</h3>
      </div>
      <form method="POST" action="{{ route('admin.promos.store') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Code</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. CATLOVE10" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm uppercase">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Type</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <select name="type" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                <option value="percent" @selected(old('type', 'percent') === 'percent')>Percent %</option>
                <option value="fixed" @selected(old('type') === 'fixed')>Fixed ₱</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Value</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="number" min="0.01" step="0.01" name="value" value="{{ old('value') }}" placeholder="10" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Expires <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reason <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="reason" value="{{ old('reason') }}" placeholder="e.g. Anniversary promo, staff discount" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <label class="flex items-center justify-between px-1">
          <span class="text-sm font-bold text-stamp-600">Active</span>
          <input type="checkbox" name="active" value="1" checked class="w-5 h-5 accent-stamp-500">
        </label>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Create Promo
        </button>
      </form>
    </div>
  </div>

  <!-- Edit Promo Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-sm p-7 relative">
      <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Edit Promo</h3>
      </div>
      <form id="editForm" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Code</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="code" id="edit_code" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm uppercase">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Type</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <select name="type" id="edit_type" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
                <option value="percent">Percent %</option>
                <option value="fixed">Fixed ₱</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Value</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="number" min="0.01" step="0.01" name="value" id="edit_value" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Expires <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="date" name="expires_at" id="edit_expires_at" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reason <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="reason" id="edit_reason" placeholder="e.g. Anniversary promo, staff discount" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
        <label class="flex items-center justify-between px-1">
          <span class="text-sm font-bold text-stamp-600">Active</span>
          <input type="checkbox" name="active" id="edit_active" value="1" class="w-5 h-5 accent-stamp-500">
        </label>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Save Changes
        </button>
      </form>
    </div>
  </div>

  <script>
    function openCreate(){ document.getElementById('createModal').classList.remove('hidden'); }

    function openEditModal(promo){
      document.getElementById('editForm').action = "{{ route('admin.promos.update', ':id') }}".replace(':id', promo.id);
      document.getElementById('edit_code').value = promo.code || '';
      document.getElementById('edit_type').value = promo.type || 'percent';
      document.getElementById('edit_value').value = promo.value || '';
      document.getElementById('edit_expires_at').value = promo.expires_at ? promo.expires_at.slice(0, 10) : '';
      document.getElementById('edit_reason').value = promo.reason || '';
      document.getElementById('edit_active').checked = !!promo.active;
      document.getElementById('editModal').classList.remove('hidden');
    }

    @if ($errors->any())
      openCreate();
    @endif
  </script>

</body>
</html>

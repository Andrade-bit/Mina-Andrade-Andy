<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Suppliers</title>
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

  @include('admin.partials.sidebar', ['active' => 'suppliers'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Suppliers</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Vendors you order cups, straws, and supplies from</p>
      </div>
      <a href="{{ route('admin.suppliers.create') }}" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-5 py-3 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Supplier
      </a>
    </div>

    <div class="flex justify-end mb-3">
      @if (request()->boolean('archived'))
        <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">&larr; Back to active</a>
      @else
        <a href="{{ route('admin.suppliers.index', ['archived' => 1]) }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">View archived</a>
      @endif
    </div>


    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="grid gap-3">
        @forelse ($suppliers as $supplier)
          <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap">
            <div class="w-11 h-11 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 shrink-0">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
            </div>
            <div class="flex-1 min-w-[200px]">
              <p class="font-extrabold text-stamp-700">{{ $supplier->supplier_name }}</p>
              <p class="text-xs text-stamp-400 font-semibold">
                {{ $supplier->contact_person ?? 'No contact person' }}
                @if ($supplier->contact_number) &middot; {{ $supplier->contact_number }} @endif
              </p>
            </div>
            @if ($supplier->email)
              <span class="bg-cream-100 text-stamp-600 text-xs font-bold px-3 py-1.5 rounded-full">{{ $supplier->email }}</span>
            @endif
            <div class="flex items-center gap-2">
              @unless ($supplier->trashed())
              <button type="button" onclick='openEditModal(@json($supplier))' class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
              @endunless
              @if ($supplier->trashed())
                <form method="POST" action="{{ route('admin.suppliers.restore', $supplier->id) }}">
                  @csrf
                  <button type="submit" class="px-3.5 py-2 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors">Restore</button>
                </form>
              @else
              <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Archive {{ $supplier->supplier_name }}? {{ $supplier->supply_purchases_count ? 'Has '.$supplier->supply_purchases_count.' purchase(s) on record. ' : '' }}You can restore it anytime.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Archive" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></button>
              </form>
              @endif
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">No suppliers yet. Click "Add Supplier" to register one.</p>
        @endforelse
      </div>
    </div>
  </main>

  <!-- Edit Supplier Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Edit Supplier</h3>
      </div>
      <form id="editForm" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Supplier Name</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="supplier_name" id="edit_supplier_name" required class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Contact Person</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="text" name="contact_person" id="edit_contact_person" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Contact No.</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
              <input type="text" name="contact_number" id="edit_contact_number" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Email</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="email" name="email" id="edit_email" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Address</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="address" id="edit_address" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Payment Terms</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="text" name="payment_terms" id="edit_payment_terms" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
          </div>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Save Changes
        </button>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(supplier){
      document.getElementById('editForm').action = "{{ route('admin.suppliers.update', ':id') }}".replace(':id', supplier.id);
      document.getElementById('edit_supplier_name').value = supplier.supplier_name || '';
      document.getElementById('edit_contact_person').value = supplier.contact_person || '';
      document.getElementById('edit_contact_number').value = supplier.contact_number || '';
      document.getElementById('edit_email').value = supplier.email || '';
      document.getElementById('edit_address').value = supplier.address || '';
      document.getElementById('edit_payment_terms').value = supplier.payment_terms || '';
      document.getElementById('editModal').classList.remove('hidden');
    }

    @if ($errors->any())
      document.getElementById('editModal').classList.remove('hidden');
    @endif
  </script>

  @include('admin.partials.toasts')

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Add Supplier</title>
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
      <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Suppliers &rsaquo; Add New</p>
      <h1 class="font-display font-bold text-xl md:text-2xl text-stamp-700">Add Supplier</h1>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.suppliers.index') }}" class="px-4 md:px-5 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
      <button type="submit" form="supplierForm" class="px-5 md:px-6 py-2.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Supplier</button>
    </div>
  </header>

  <main class="max-w-2xl mx-auto p-5 md:p-8">


    <form id="supplierForm" method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-6">
      @csrf

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-5">Company Info</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Supplier / Company Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="e.g. Davao Cup Traders" required autofocus class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Contact Person</label>
              <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              </div>
            </div>
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Phone Number</label>
              <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
                <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="09xx-xxx-xxxx" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
              </div>
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Email <span class="text-stamp-300 normal-case font-semibold">(optional)</span></label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Address <span class="text-stamp-300 normal-case font-semibold">(optional)</span></label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
              <input type="text" name="address" value="{{ old('address') }}" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-soft p-6 md:p-7">
        <h2 class="font-display font-bold text-stamp-700 text-lg mb-5">Terms</h2>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Default Payment Terms</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
            <input type="text" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="e.g. Net 30, Cash on Delivery" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 pb-6">
        <a href="{{ route('admin.suppliers.index') }}" class="px-5 py-3 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-display font-bold text-sm transition-colors">Cancel</a>
        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Save Supplier</button>
      </div>
    </form>
  </main>

  @include('admin.partials.card-strokes')
  @include('admin.partials.toasts')

</body>
</html>

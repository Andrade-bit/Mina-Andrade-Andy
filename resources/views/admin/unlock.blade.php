<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - Dashboard locked</title>
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
          'soft-inset': 'inset 0 2px 6px rgba(26,59,82,0.14), inset 0 -1px 1px rgba(255,255,255,0.7)',
          'soft-btn': '0 5px 0 #1A3B52, 0 10px 18px rgba(47,102,144,0.3)',
        },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex items-center justify-center p-5">

  <main class="w-full max-w-md bg-white rounded-[2rem] shadow-soft p-7 md:p-9">
    <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset flex items-center justify-center text-stamp-600 mb-5">
      <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <h1 class="font-display font-bold text-2xl text-stamp-700">Dashboard locked</h1>
    <p class="text-sm font-semibold text-stamp-500 mt-1.5">The POS was opened on this device, so the admin dashboard was locked. Enter the admin password to continue.</p>

    <form method="POST" action="{{ route('admin.unlock.store') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <label for="password" class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Admin password</label>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3">
          <input id="password" type="password" name="password" required autofocus autocomplete="current-password" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
        </div>
      </div>
      <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">Unlock dashboard</button>
    </form>

    <div class="mt-5 flex items-center justify-between text-sm font-bold">
      <a href="{{ route('pos.login') }}" class="text-stamp-500 hover:text-stamp-700 underline underline-offset-2">Back to POS</a>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="text-stamp-500 hover:text-stamp-700 underline underline-offset-2">Log out</button>
      </form>
    </div>
  </main>

  @include('admin.partials.toasts')

</body>
</html>

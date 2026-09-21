<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews Admin Login</title>
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
          mint: { 500: '#6FAE8B', 600: '#3F7457' },
          coral: { 500: '#E0776B', 600: '#9C4A41' },
        },
        boxShadow: {
          soft: '0 16px 32px -10px rgba(26,59,82,0.18), 0 4px 10px rgba(26,59,82,0.08)',
          'soft-sm': '0 6px 14px -4px rgba(26,59,82,0.15)',
          'soft-inset': 'inset 0 2px 6px rgba(26,59,82,0.14), inset 0 -1px 1px rgba(255,255,255,0.7)',
          'soft-btn': '0 5px 0 #1A3B52, 0 10px 18px rgba(47,102,144,0.35)',
        },
        keyframes: {
          float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-5px)' } },
        },
        animation: { float: 'float 3.5s ease-in-out infinite' },
      }
    }
  }
</script>
</head>
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 flex items-center justify-center p-6 relative overflow-hidden">

  <div class="absolute -top-28 -left-24 w-80 h-80 bg-stamp-100 rounded-full blur-3xl opacity-70"></div>
  <div class="absolute -bottom-28 -right-20 w-96 h-96 bg-mint-500/10 rounded-full blur-3xl"></div>
  <div class="absolute top-1/3 right-10 w-24 h-24 bg-stamp-300/10 rounded-full blur-2xl hidden md:block"></div>

  <div class="relative w-full max-w-sm bg-cream-50 rounded-[2rem] shadow-soft p-10">

    <div class="flex flex-col items-center mb-8">
      <div class="animate-float w-20 h-20 rounded-full bg-cream-100 shadow-soft-inset ring-4 ring-white flex items-center justify-center text-stamp-600 mb-4">
        <svg viewBox="0 0 64 64" class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 24 L16 10 L28 20"/>
          <path d="M44 24 L48 10 L36 20"/>
          <path d="M24 16 Q32 8 40 16"/>
          <circle cx="32" cy="8" r="3.2"/>
          <circle cx="32" cy="36" r="17"/>
          <circle cx="26" cy="34" r="1.6" fill="currentColor" stroke="none"/>
          <circle cx="38" cy="34" r="1.6" fill="currentColor" stroke="none"/>
          <path d="M30 40 Q32 42 34 40"/>
          <path d="M9 32 L19 34 M9 38 L19 36" opacity="0.6"/>
          <path d="M55 32 L45 34 M55 38 L45 36" opacity="0.6"/>
        </svg>
      </div>
      <h1 class="font-display font-bold text-3xl text-stamp-700">Catbrews</h1>
      <p class="font-body text-xs tracking-[0.25em] uppercase text-stamp-500 font-bold mt-1">Admin Login</p>
    </div>

    <form class="space-y-5">
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-2 ml-1">Username</label>
        <div class="flex items-center gap-3 bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3.5">
          <svg class="w-5 h-5 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <input type="text" placeholder="admin.catbrews" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
        </div>
      </div>

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-2 ml-1">Password</label>
        <div class="flex items-center gap-3 bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-3.5">
          <svg class="w-5 h-5 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <input type="password" value="••••••••" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
        </div>
      </div>

      <div class="flex items-center justify-between px-1 pt-1">
        <div class="flex items-center gap-3">
          <div class="w-11 h-6 bg-stamp-100 rounded-full shadow-soft-inset relative">
            <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow-soft-sm translate-x-5"></span>
          </div>
          <span class="text-sm font-bold text-stamp-600">Remember me</span>
        </div>
        <a href="#" class="text-xs font-extrabold text-stamp-500 hover:text-stamp-700">Forgot?</a>
      </div>

      <a href="{{ route('admin.users') }}" class="block text-center bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold tracking-wide text-sm py-4 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
        Log In
      </a>
    </form>

    <p class="text-center text-[11px] font-bold text-stamp-300 mt-7">
      Starting a shift instead? <a href="{{ route('pos.login') }}" class="text-stamp-500 hover:text-stamp-700 underline">Open POS Access</a>
    </p>
    <p class="text-center text-[11px] font-bold text-stamp-300 mt-1">Catbrews · Coffee &amp; Beverage Stall</p>
  </div>

</body>
</html>

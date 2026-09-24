<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews POS Access</title>
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
          'soft-sm': '0 6px 14px -4px rgba(26,59,82,0.15)',
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

  <button type="button" onclick="history.back()" class="absolute top-6 left-6 z-10 w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 shadow-soft-sm flex items-center justify-center text-stamp-500 transition-colors" title="Back">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
  </button>

  <div class="relative w-full max-w-md bg-cream-50 rounded-[2rem] shadow-soft p-10">

    <div class="flex flex-col items-center mb-7">
      <div class="w-16 h-16 rounded-full bg-cream-100 shadow-soft-inset ring-4 ring-white flex items-center justify-center text-stamp-600 mb-3">
        <svg viewBox="0 0 64 64" class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
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
      <h1 class="font-display font-bold text-2xl text-stamp-700">Catbrews POS</h1>
      <p class="font-body text-xs tracking-[0.2em] uppercase text-stamp-500 font-bold mt-1">Enter your PIN</p>
    </div>

    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3 text-center">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('pos.login.store') }}" id="pinForm">
      @csrf
      <input type="hidden" name="passcode" id="passcodeInput" value="">

      <p class="text-center text-[11px] text-stamp-300 font-semibold mb-4">Your PIN identifies you on every sale you process</p>

      <div class="flex items-center justify-center gap-3 mb-6">
        <span id="dot0" class="w-3.5 h-3.5 rounded-full bg-cream-200 transition-colors"></span>
        <span id="dot1" class="w-3.5 h-3.5 rounded-full bg-cream-200 transition-colors"></span>
        <span id="dot2" class="w-3.5 h-3.5 rounded-full bg-cream-200 transition-colors"></span>
        <span id="dot3" class="w-3.5 h-3.5 rounded-full bg-cream-200 transition-colors"></span>
      </div>

      <div class="grid grid-cols-3 gap-3 max-w-[220px] mx-auto mb-6">
        <button type="button" onclick="pressDigit('1')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">1</button>
        <button type="button" onclick="pressDigit('2')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">2</button>
        <button type="button" onclick="pressDigit('3')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">3</button>
        <button type="button" onclick="pressDigit('4')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">4</button>
        <button type="button" onclick="pressDigit('5')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">5</button>
        <button type="button" onclick="pressDigit('6')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">6</button>
        <button type="button" onclick="pressDigit('7')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">7</button>
        <button type="button" onclick="pressDigit('8')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">8</button>
        <button type="button" onclick="pressDigit('9')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">9</button>
        <button type="button" onclick="clearPin()" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-coral-500 text-xs transition-all active:shadow-none active:translate-y-[2px]">Clear</button>
        <button type="button" onclick="pressDigit('0')" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px]">0</button>
        <button type="button" onclick="backspace()" class="w-14 h-14 rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-lg transition-all active:shadow-none active:translate-y-[2px] flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l-7-7 7-7m-7 7h18"/></svg>
        </button>
      </div>

      <button type="submit" class="block w-full text-center bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold tracking-wide text-sm py-3.5 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
        Unlock POS
      </button>
    </form>

    <p class="text-center text-[11px] font-bold text-stamp-300 mt-7">
      Managing the shop instead? <a href="{{ route('admin.login') }}" class="text-stamp-500 hover:text-stamp-700 underline">Admin Dashboard Login</a>
    </p>
  </div>

  <script>
    let pin = '';
    function updateDots(){
      for (let i = 0; i < 4; i++) {
        const dot = document.getElementById('dot' + i);
        dot.classList.toggle('bg-stamp-500', i < pin.length);
        dot.classList.toggle('bg-cream-200', i >= pin.length);
      }
      document.getElementById('passcodeInput').value = pin;
    }
    function pressDigit(d){
      if (pin.length < 4) {
        pin += d;
        updateDots();
        if (pin.length === 4) {
          document.getElementById('pinForm').submit();
        }
      }
    }
    function clearPin(){ pin = ''; updateDots(); }
    function backspace(){ pin = pin.slice(0, -1); updateDots(); }
  </script>

</body>
</html>

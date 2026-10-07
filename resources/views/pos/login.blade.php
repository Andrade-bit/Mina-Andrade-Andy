<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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
          coral: { 50: '#FBEDEB', 500: '#E0776B', 600: '#9C4A41' },
        },
        boxShadow: {
          'soft-sm': '0 6px 14px -4px rgba(26,59,82,0.15)',
          'soft-btn': '0 5px 0 rgba(26,59,82,0.35), 0 14px 24px rgba(26,59,82,0.2)',
        },
      }
    }
  }
</script>
<style>
  /* Layout lives in plain CSS so it behaves the same on every browser and device. */
  *, *::before, *::after { box-sizing: border-box; }
  html { -webkit-text-size-adjust: 100%; }
  body { margin: 0; background: radial-gradient(900px 520px at 85% 12%, #E4C7A1 0%, rgba(228,199,161,0) 70%), radial-gradient(800px 500px at 0% 100%, #F4E9DB 0%, rgba(244,233,219,0) 70%), #F1E6D8; background-attachment: fixed; }

  .shell { min-height: 100vh; min-height: 100dvh; display: flex; flex-direction: column; }
  .topbar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px 12px;
            padding: max(16px, env(safe-area-inset-top, 0px)) clamp(16px, 4vw, 40px) 8px; }
  .brand { display: inline-flex; align-items: center; gap: 12px; font-family: 'Baloo 2', sans-serif; font-weight: 700; font-size: 1.25rem; line-height: 1; color: #1A3B52; text-decoration: none; }
  .brand-mark { width: 40px; height: 40px; border-radius: 50%; background: #1A3B52; display: grid; place-items: center; flex: none; }
  .back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 14px; font-weight: 800; font-size: .875rem; color: #1A3B52; text-decoration: none; white-space: nowrap; transition: background .2s; }
  .back:hover { background: rgba(26,59,82,.1); }

  .stage { flex: 1; display: grid; place-items: center; padding: 12px clamp(16px, 4vw, 40px) 28px; }
  .split { width: 100%; max-width: 1040px; display: grid; grid-template-columns: minmax(0, 1fr); gap: 40px; justify-items: center; }
  .card { width: 100%; max-width: 460px; padding: clamp(22px, 5vw, 40px); border-radius: 2rem; background: rgba(255,253,249,.86);
          border: 1px solid rgba(26,59,82,.1); box-shadow: 0 24px 48px -16px rgba(26,59,82,.22), 0 4px 10px rgba(26,59,82,.06);
          -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); }
  .art { display: none; position: relative; width: 100%; max-width: 380px; justify-self: center; }
  .foot { padding: 8px 16px max(16px, env(safe-area-inset-bottom, 0px)); text-align: center; font-size: .75rem; font-weight: 700; color: rgba(47,102,144,.85); }

  .eyebrow { font-size: .75rem; letter-spacing: .2em; text-transform: uppercase; font-weight: 800; color: #2F6690; margin: 0; }
  .title { font-family: 'Baloo 2', sans-serif; font-weight: 800; font-size: clamp(2rem, 8vw, 3rem); line-height: 1; margin: 8px 0 0; color: #1A3B52; text-wrap: balance; }
  .lede { margin: 12px 0 0; font-weight: 600; color: #2F6690; }

  /* PIN pad: keys shrink on short screens so the whole pad always fits */
  .dots { display: flex; align-items: center; justify-content: center; gap: 16px; margin: 0 0 clamp(14px, 3vh, 24px); }
  .pad { display: grid; grid-template-columns: repeat(3, auto); justify-content: center; gap: clamp(8px, 1.6vh, 12px) 12px; margin: 0 auto clamp(16px, 3vh, 28px); }
  .key { width: clamp(52px, 9vh, 64px); height: clamp(52px, 9vh, 64px); display: flex; align-items: center; justify-content: center; }

  @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-12px) rotate(-1deg); } }
  @keyframes drift { 0%, 100% { transform: translateY(0) rotate(var(--r)); } 50% { transform: translateY(-10px) rotate(calc(var(--r) + 25deg)); } }
  .rise { animation: rise .7s cubic-bezier(.2,.8,.2,1) both; }
  .bob { animation: bob 7s ease-in-out infinite; }
  .bean { position: absolute; width: 34px; height: 21px; border-radius: 50%; background: radial-gradient(ellipse at 35% 30%, #9a6238, #5e3519); animation: drift 6s ease-in-out infinite; }
  .bean::after { content: ""; position: absolute; left: 10%; right: 10%; top: 47%; height: 2px; border-radius: 2px; background: #2e1a0c; transform: rotate(-9deg); }
  .photo { display: block; width: 100%; aspect-ratio: 4 / 5; object-fit: cover; object-position: 50% 64%; border-radius: 2.5rem; border: 10px solid #FFFDF9; box-shadow: 0 24px 48px -16px rgba(26,59,82,.28); }
  .caption { position: absolute; left: 20px; bottom: -16px; display: inline-flex; padding: 8px 16px; border-radius: 999px; background: rgba(255,253,249,.95); border: 1px solid rgba(26,59,82,.1); box-shadow: 0 6px 14px -4px rgba(26,59,82,.15); font-family: 'Baloo 2', sans-serif; font-weight: 700; color: #1A3B52; }
  .glow { position: absolute; inset: -24px; border-radius: 50%; background: rgba(228,199,161,.6); filter: blur(48px); }

  @media (min-width: 1024px) {
    .split { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 72px; justify-items: stretch; align-items: center; }
    .art { display: block; }
  }
  @media (max-width: 360px) {
    .back span { display: none; }
    .card { border-radius: 1.5rem; }
  }
  @media (max-height: 540px) {
    .stage { place-items: start center; padding-top: 4px; padding-bottom: 12px; }
    .topbar { padding-top: max(8px, env(safe-area-inset-top, 0px)); padding-bottom: 2px; }
    .brand-mark { width: 34px; height: 34px; }
    .card { padding: 16px 22px; }
    .title { font-size: 1.75rem; }
    .lede { margin-top: 6px; }
    .art { display: none !important; }
  }
  /* phones held sideways: heading on the left, form on the right, so nothing is pushed below the fold */
  @media (max-height: 540px) and (min-width: 640px) {
    .card { max-width: 760px; display: grid; grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr); column-gap: 28px; align-items: start; }
    .card > .eyebrow, .card > .title, .card > .lede, .card > .alert, .card > .alt { grid-column: 1; }
    .card > form { grid-column: 2; grid-row: 1 / span 6; margin-top: 0; }
    .card form > * + * { margin-top: 12px !important; }
    .field { padding-top: 10px !important; padding-bottom: 10px !important; }
    .alt { margin-top: 14px !important; text-align: left !important; }
    .key { width: 42px; height: 42px; font-size: 1.05rem; }
    .pad { gap: 8px 10px; margin-bottom: 14px; }
    .dots { margin-bottom: 12px; }
    .card button[type=submit] { padding-top: 10px !important; padding-bottom: 10px !important; }
  }
  @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
</style>
</head>
<body class="font-body text-stamp-700">
@php
  $backUrl = auth()->check() ? route('admin.dashboard') : route('home');
  $backLabel = auth()->check() ? 'Back to dashboard' : 'Back to home';
@endphp
<div class="shell">

  <header class="topbar">
    <a href="{{ $backUrl }}" class="brand">
      <span class="brand-mark">
        <svg viewBox="0 0 64 64" width="24" height="24" fill="none" stroke="#FFFDF9" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 24 L16 10 L28 20"/><path d="M44 24 L48 10 L36 20"/><path d="M24 16 Q32 8 40 16"/>
          <circle cx="32" cy="8" r="3.2"/><circle cx="32" cy="36" r="17"/>
          <circle cx="26" cy="34" r="1.6" fill="#FFFDF9" stroke="none"/><circle cx="38" cy="34" r="1.6" fill="#FFFDF9" stroke="none"/>
          <path d="M30 40 Q32 42 34 40"/>
        </svg>
      </span>
      Catbrews
    </a>
    <a href="{{ $backUrl }}" class="back" aria-label="{{ $backLabel }}">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5m6-6l-6 6 6 6"/></svg>
      <span>{{ $backLabel }}</span>
    </a>
  </header>

  <main class="stage">
    <div class="split">

      <section class="card rise">
        <p class="eyebrow">POS access</p>
        <h1 class="title">Start your shift.</h1>
        <p class="lede">Enter your PIN. It identifies you on every sale you process.</p>

        <form method="POST" action="{{ route('pos.login.store') }}" id="pinForm" class="mt-6">
          @csrf
          <input type="hidden" name="passcode" id="passcodeInput" value="">

          <div class="dots" aria-hidden="true">
            @foreach ([0, 1, 2, 3] as $dot)
              <span id="dot{{ $dot }}" class="w-4 h-4 rounded-full bg-cream-200 border-2 border-stamp-700/20 transition-colors"></span>
            @endforeach
          </div>

          <div class="pad">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $digit)
              <button type="button" onclick="pressDigit('{{ $digit }}')" class="key rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-xl transition-all hover:-translate-y-0.5 active:shadow-none active:translate-y-[2px]">{{ $digit }}</button>
            @endforeach
            <button type="button" onclick="clearPin()" class="key rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-coral-500 text-sm transition-all hover:-translate-y-0.5 active:shadow-none active:translate-y-[2px]">Clear</button>
            <button type="button" onclick="pressDigit('0')" class="key rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 text-xl transition-all hover:-translate-y-0.5 active:shadow-none active:translate-y-[2px]">0</button>
            <button type="button" onclick="backspace()" aria-label="Delete last digit" class="key rounded-full bg-cream-100 shadow-soft-sm font-display font-bold text-stamp-700 transition-all hover:-translate-y-0.5 active:shadow-none active:translate-y-[2px]">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l-7-7 7-7m-7 7h18"/></svg>
            </button>
          </div>

          <button type="submit" class="block w-full text-center bg-stamp-700 text-cream-50 font-display font-bold tracking-wide text-lg py-3.5 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] hover:-translate-y-0.5 transition-all duration-150">
            Unlock POS
          </button>
        </form>

        @guest
          <p class="alt text-center text-sm font-bold text-stamp-500 mt-6">
            Managing the shop instead? <a href="{{ route('admin.login') }}" class="text-stamp-700 underline underline-offset-2 hover:text-stamp-500">Admin Dashboard Login</a>
          </p>
        @endguest
      </section>

      <figure class="art rise" style="animation-delay:.15s; margin:0" aria-hidden="true">
        <div class="glow"></div>
        <div class="bob" style="position:relative">
          <img src="{{ asset('images/iced-mocha.webp') }}" alt="" class="photo">
          <figcaption class="caption">Iced Mocha Latte</figcaption>
        </div>
        <span class="bean" style="left:-34px;top:12%;--r:-20deg"></span>
        <span class="bean" style="right:-30px;top:26%;--r:35deg;animation-delay:-2s"></span>
        <span class="bean" style="left:-18px;bottom:20%;--r:70deg;animation-delay:-4s"></span>
        <span class="bean" style="right:-12px;bottom:8%;--r:-45deg;animation-delay:-1s"></span>
      </figure>

    </div>
  </main>

  <footer class="foot">Catbrews &middot; Coffee &amp; Beverage Stall</footer>
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
          document.getElementById('pinForm').requestSubmit();
        }
      }
    }
    function clearPin(){ pin = ''; updateDots(); }
    function backspace(){ pin = pin.slice(0, -1); updateDots(); }
    document.getElementById('pinForm').addEventListener('form:invalid', clearPin);
  </script>

  @include('admin.partials.pos-faq')
  @include('admin.partials.toasts')

</body>
</html>

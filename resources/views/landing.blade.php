<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Catbrews | Coffee &amp; Beverage Stall</title>
<meta name="description" content="Catbrews coffee and beverage stall: hot and iced coffee, non-coffee drinks and fruit juice, made to order.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  /* Concept: a coffee-stall website that opens with the iced mocha being made (sticky 3D scene), then settles into
     calm latte-toned sections: About, Menu, Services, Visit. One bold moment on top, quiet pages below. */
  :root{
    --bg:#F1E6D8;
    --ink:#1A3B52;
    --ink-soft:#3D5F78;
    --cream:#FFFDF9;
    --latte:#E4C7A1;
    --foam:#F4E9DB;
    --glass:rgba(255,253,249,.74);
    --line:rgba(26,59,82,.16);
    --display:'Baloo 2',ui-rounded,system-ui,sans-serif;
    --body:'Nunito',system-ui,sans-serif;
    color-scheme:light;
  }
  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{margin:0;background:var(--bg);color:var(--ink);font:400 16px/1.6 var(--body)}
  a{color:inherit;text-decoration:none}
  img{display:block}
  [hidden]{display:none!important}
  :focus-visible{outline:3px solid var(--ink);outline-offset:3px;border-radius:12px}
  section[id]{scroll-margin-top:72px}
  h1,h2,h3{font-family:var(--display);margin:0;text-wrap:balance}
  .kicker{font-weight:800;font-size:12px;letter-spacing:.16em;text-transform:uppercase;opacity:.75}
  .wrap{width:min(1160px,100%);margin:0 auto;padding-inline:clamp(16px,4vw,40px)}

  /* ---------- nav ---------- */
  .nav{position:fixed;top:0;left:0;right:0;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:16px;
       padding:calc(10px + env(safe-area-inset-top,0px)) clamp(16px,4vw,40px) 10px;
       background:var(--glass);border-bottom:1px solid var(--line);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)}
  .brand{display:flex;align-items:center;gap:12px;font:700 22px/1 var(--display)}
  .logo{width:40px;height:40px;border-radius:50%;background:var(--ink);display:grid;place-items:center}
  .links{display:flex;align-items:center;gap:6px}
  .links a{padding:8px 14px;border-radius:12px;font-weight:800;font-size:15px;opacity:.75;transition:opacity .2s,background .2s}
  .links a:hover{opacity:1;background:rgba(26,59,82,.07)}
  .links a.on{opacity:1;background:rgba(26,59,82,.1)}
  .nav-right{display:flex;align-items:center;gap:10px}
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;padding:0 20px;border-radius:16px;border:0;background:var(--ink);color:var(--cream);
       font:700 16px/1 var(--display);cursor:pointer;box-shadow:0 5px 0 rgba(26,59,82,.35),0 14px 24px rgba(26,59,82,.18);transition:transform .2s}
  .btn:hover{transform:translateY(-2px)}
  .burger{display:none;width:44px;height:44px;border-radius:14px;border:2px solid var(--ink);background:transparent;color:var(--ink);cursor:pointer;place-items:center}

  /* ---------- home: sticky 3D recipe ---------- */
  .recipe{height:560vh;height:560svh;position:relative}
  .stage{position:sticky;top:0;height:100vh;height:100svh;overflow:hidden}
  #gl{position:absolute;inset:0;width:100%;height:100%;display:block;pointer-events:none}
  .copy{position:absolute;z-index:2;left:clamp(16px,4vw,40px);top:50%;transform:translateY(-50%);width:min(470px,46vw)}
  h1{margin-top:10px;font-size:clamp(40px,6.2vw,82px);line-height:.98;font-weight:800}
  .desc{margin:16px 0 0;max-width:420px;font-size:clamp(17px,1.9vw,20px);font-weight:600;color:var(--ink-soft)}
  .copy .swap{transition:opacity .35s,transform .35s}
  .copy.out .swap{opacity:0;transform:translateY(8px)}
  .cta{margin-top:22px}
  .rail{position:absolute;z-index:2;right:clamp(16px,3vw,36px);top:50%;transform:translateY(-50%);margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:18px}
  .rail li{display:flex;align-items:center;justify-content:flex-end;gap:12px;font-weight:800;font-size:13px;letter-spacing:.04em;opacity:.42;transition:opacity .3s}
  .rail li i{width:12px;height:12px;border-radius:50%;border:2px solid var(--ink);transition:background .3s,transform .3s}
  .rail li.done{opacity:.8}
  .rail li.done i{background:var(--ink)}
  .rail li.on{opacity:1}
  .rail li.on i{background:var(--ink);transform:scale(1.35)}
  .hint{position:absolute;z-index:2;left:50%;bottom:calc(20px + env(safe-area-inset-bottom,0px));transform:translateX(-50%);display:flex;align-items:center;gap:10px;
        font-weight:800;font-size:12px;letter-spacing:.16em;text-transform:uppercase;opacity:.7;transition:opacity .4s}
  .hint i{display:block;width:2px;height:30px;background:var(--ink);transform-origin:top;animation:hint 2.2s ease-in-out infinite}
  .hint.gone{opacity:0}
  @keyframes hint{0%{transform:scaleY(0)}60%{transform:scaleY(1)}100%{transform:scaleY(1);opacity:0}}
  .fallback{display:none;position:absolute;z-index:0;right:8vw;top:22vh;width:min(34vw,380px);aspect-ratio:1;max-width:100%;border-radius:48px;background:linear-gradient(#e8c9a0,#7a4a2a)}
  .no-gl .fallback{display:block}

  /* ---------- sections ---------- */
  .sec{padding-block:clamp(72px,10vw,120px)}
  .sec.latte{background:var(--latte)}
  .sec.foam{background:var(--foam)}
  h2{margin-top:10px;font-size:clamp(32px,4.8vw,58px);line-height:1.02;font-weight:800}
  .sub{margin:14px 0 0;max-width:520px;font-size:18px;font-weight:600;color:var(--ink-soft)}

  .about{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:clamp(28px,5vw,72px);align-items:center}
  .about p{margin:16px 0 0;max-width:520px;font-size:17px;color:var(--ink-soft);font-weight:600}
  .facts{list-style:none;margin:26px 0 0;padding:0;display:grid;gap:12px}
  .facts li{display:flex;align-items:flex-start;gap:12px;font-weight:700}
  .facts svg{flex:none;margin-top:2px}
  .photo{margin:0;position:relative}
  .photo img{width:100%;aspect-ratio:4/5;object-fit:cover;object-position:50% 64%;border-radius:36px;box-shadow:0 30px 60px -22px rgba(26,59,82,.45)}
  .photo figcaption{position:absolute;left:16px;bottom:16px;padding:8px 16px;border-radius:999px;background:var(--glass);border:1px solid var(--line);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);font:700 15px var(--display)}

  .feature{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:clamp(20px,4vw,48px);align-items:center;margin-top:36px;padding:clamp(16px,2.4vw,26px);border-radius:34px;background:var(--glass);border:1px solid var(--line)}
  .feature img{width:100%;aspect-ratio:1;object-fit:cover;object-position:50% 62%;border-radius:26px}
  .feature h3{font-size:clamp(26px,3.4vw,40px);line-height:1.05}
  .feature p{margin:10px 0 0;color:var(--ink-soft);font-weight:600;max-width:440px}
  .tag{display:inline-block;margin-bottom:10px;padding:5px 12px;border-radius:999px;background:var(--ink);color:var(--cream);font-weight:800;font-size:11px;letter-spacing:.14em;text-transform:uppercase}
  .price-ph{display:inline-block;margin-top:14px;padding:8px 16px;border-radius:999px;border:2px dashed var(--ink);font:700 16px var(--display)}

  .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-top:34px}
  .tab{min-height:44px;padding:0 20px;border-radius:999px;border:2px solid var(--ink);background:transparent;color:var(--ink);font:700 16px var(--display);cursor:pointer;transition:background .2s,color .2s}
  .tab[aria-selected="true"]{background:var(--ink);color:var(--cream)}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:18px;margin-top:22px}
  .item{border-radius:26px;background:var(--glass);border:1px solid var(--line);padding:12px 12px 18px;transition:transform .35s cubic-bezier(.2,.8,.2,1),box-shadow .35s}
  .item:hover{transform:translateY(-6px);box-shadow:0 24px 40px -18px rgba(26,59,82,.35)}
  .item img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:18px}
  .item h3{margin:14px 6px 0;font-size:20px;line-height:1.15}
  .sizes{display:flex;gap:6px;flex-wrap:wrap;margin:10px 6px 0}
  .sz{padding:4px 10px;border-radius:999px;background:rgba(26,59,82,.09);font-weight:800;font-size:12px;font-variant-numeric:tabular-nums}
  .noimg{aspect-ratio:1;border-radius:18px;background:linear-gradient(135deg,#e8c9a0,#b9824d)}
  .feature .sizes{margin:16px 0 0}
  .note{margin:22px 0 0;font-size:14px;font-weight:700;color:var(--ink-soft)}

  .services{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:18px;margin-top:38px}
  .svc{padding:28px;border-radius:30px;background:var(--glass);border:1px solid var(--line)}
  .svc .ico{width:54px;height:54px;border-radius:18px;background:var(--ink);display:grid;place-items:center}
  .svc h3{margin-top:18px;font-size:22px}
  .svc p{margin:8px 0 0;color:var(--ink-soft);font-weight:600;font-size:15.5px}

  .info{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:18px;margin-top:36px}
  .info .svc h3{margin-top:14px}
  footer{margin-top:clamp(56px,8vw,90px);padding-top:28px;border-top:1px solid var(--line);display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;font-weight:700}
  footer nav{display:flex;gap:18px;flex-wrap:wrap;font-size:15px}
  footer a:hover{text-decoration:underline}

  @media (max-width:820px){
    .links{display:none;position:absolute;top:100%;left:0;right:0;flex-direction:column;align-items:stretch;gap:2px;padding:10px clamp(16px,4vw,40px) 16px;background:var(--cream);border-bottom:1px solid var(--line)}
    .nav.open .links{display:flex}
    .links a{padding:12px 14px;font-size:17px}
    .burger{display:grid}
    .copy{top:auto;bottom:calc(64px + env(safe-area-inset-bottom,0px));transform:none;left:16px;right:16px;width:auto;
          background:var(--glass);border:1px solid var(--line);border-radius:26px;padding:18px 20px;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}
    h1{font-size:clamp(30px,8.4vw,44px)}
    .desc{margin-top:8px;font-size:16px}
    .cta{margin-top:14px}
    .rail{top:auto;right:auto;left:50%;bottom:calc(22px + env(safe-area-inset-bottom,0px));transform:translateX(-50%);flex-direction:row;gap:14px}
    .rail li span{display:none}
    .hint{display:none}
    .no-gl .fallback{right:auto;left:50%;transform:translateX(-50%);top:16vh;width:min(56vw,260px)}
    .about,.feature{grid-template-columns:minmax(0,1fr)}
    .grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .item{padding:8px 8px 14px}
    .item h3{font-size:17px;margin:10px 4px 0}
    .sizes{margin:8px 4px 0}
    .photo{order:-1}
    .photo img{aspect-ratio:1}
  }
  @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>
<!-- ============ NAV ============ -->
<header class="nav" id="nav">
  <a class="brand" href="#home">
    <span class="logo">
      <svg viewBox="0 0 64 64" width="25" height="25" fill="none" stroke="#FFFDF9" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 24 L16 10 L28 20"/><path d="M44 24 L48 10 L36 20"/><path d="M24 16 Q32 8 40 16"/><circle cx="32" cy="8" r="3.2"/><circle cx="32" cy="36" r="17"/><circle cx="26" cy="34" r="1.6" fill="#FFFDF9" stroke="none"/><circle cx="38" cy="34" r="1.6" fill="#FFFDF9" stroke="none"/><path d="M30 40 Q32 42 34 40"/></svg>
    </span>
    Catbrews
  </a>
  <nav class="links" id="links" aria-label="Main">
    <a href="#home" data-s="home">Home</a>
    <a href="#about" data-s="about">About</a>
    <a href="#menu" data-s="menu">Menu</a>
    <a href="#services" data-s="services">Services</a>
    <a href="#visit" data-s="visit">Visit</a>
  </nav>
  <div class="nav-right">
    <a class="btn" href="{{ route('admin.login') }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
      Login
    </a>
    <button class="burger" id="burger" type="button" aria-expanded="false" aria-controls="links" aria-label="Open menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
  </div>
</header>

<!-- ============ HOME: the cup is made as you scroll ============ -->
<section class="recipe" id="home">
  <div class="stage" id="stage">
    <canvas id="gl" aria-hidden="true"></canvas>
    <div class="fallback" aria-hidden="true"></div>

    <div class="copy" id="copy" aria-live="polite">
      <div class="swap">
        <div class="kicker" id="kicker">Iced Mocha Latte</div>
        <h1 id="title">Watch it come together.</h1>
        <p class="desc" id="desc">Scroll to make one, layer by layer.</p>
        <a class="btn cta" id="cta" href="#menu" hidden>See our menu</a>
      </div>
    </div>

    <ol class="rail" aria-label="Recipe steps">
      <li data-i="1"><span>Ice</span><i></i></li>
      <li data-i="2"><span>Chocolate</span><i></i></li>
      <li data-i="3"><span>Espresso &amp; milk</span><i></i></li>
      <li data-i="4"><span>Foam</span><i></i></li>
      <li data-i="5"><span>Lid</span><i></i></li>
    </ol>
    <div class="hint" id="hint" aria-hidden="true"><i></i>Scroll</div>
  </div>
</section>

<main>
  <!-- ============ ABOUT ============ -->
  <section class="sec latte" id="about">
    <div class="wrap about">
      <div>
        <div class="kicker">About</div>
        <h2>A small stall that makes every cup to order.</h2>
        <p>{{ $story ?? 'Catbrews is a coffee and beverage stall serving hot and iced coffee, non-coffee drinks and fresh fruit juice, made to order.' }}</p>
        <ul class="facts">
          <li><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>@if ($groups->isNotEmpty())On the menu: {{ $groups->pluck('name')->map(fn ($name) => strtolower($name))->join(', ', ' and ') }}@else Hot and iced drinks, made to order @endif</li>
          <li><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>Choose your size{{ $lowestPrice ? ', from ₱'.$lowestPrice : '' }}</li>
          <li><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>Pay with cash, GCash or card</li>
        </ul>
      </div>
      <figure class="photo">
        <img src="{{ asset('images/iced-mocha.webp') }}" alt="An iced mocha latte in a clear cup with chocolate streaks, foam on top and the Catbrews cat logo printed on the cup">
        <figcaption>Our Iced Mocha Latte</figcaption>
      </figure>
    </div>
  </section>

  <!-- ============ MENU ============ -->
  <section class="sec foam" id="menu">
    <div class="wrap">
      <div class="kicker">What we sell</div>
      <h2>The menu.</h2>
      <p class="sub">Prices are by size. Order at the counter.</p>

      <div class="feature">
        <img src="{{ asset('images/iced-mocha.webp') }}" alt="Iced mocha latte in a clear cup">
        <div>
          <span class="tag">Signature</span>
          <h3>{{ $signature['name'] ?? 'Iced Mocha Latte' }}</h3>
          <p>Dark chocolate down the wall, espresso and cold milk poured over ice, finished with a layer of foam.</p>
          @if ($signature)
            <div class="sizes">@foreach ($signature['sizes'] as $size)<span class="sz">{{ $size['label'] }} &#8369;{{ $size['price'] }}</span>@endforeach</div>
          @endif
        </div>
      </div>

      @if ($groups->isNotEmpty())
      <div class="tabs" role="tablist" aria-label="Drink groups">
        @foreach ($groups as $group)
          <button class="tab" role="tab" id="t-{{ $group['slug'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-controls="p-{{ $group['slug'] }}" data-p="p-{{ $group['slug'] }}" type="button">{{ $group['name'] }}</button>
        @endforeach
      </div>
      @foreach ($groups as $group)
        <div class="grid" id="p-{{ $group['slug'] }}" role="tabpanel" aria-labelledby="t-{{ $group['slug'] }}" @if (! $loop->first) hidden @endif>
          @foreach ($group['items'] as $item)
            <article class="item">
              @if ($item['image'])<img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">@else<div class="noimg" aria-hidden="true"></div>@endif
              <h3>{{ $item['name'] }}</h3>
              <div class="sizes">@foreach ($item['sizes'] as $size)<span class="sz">{{ $size['label'] }} &#8369;{{ $size['price'] }}</span>@endforeach</div>
            </article>
          @endforeach
        </div>
      @endforeach
      @else
      <p class="sub">Our menu is being updated. Check back soon.</p>
      @endif
    </div>
  </section>

  <!-- ============ SERVICES ============ -->
  <section class="sec latte" id="services">
    <div class="wrap">
      <div class="kicker">Services</div>
      <h2>How we serve you.</h2>
      <div class="services">
        <div class="svc">
          <div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFDF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h11v6a5 5 0 01-5 5H10a5 5 0 01-5-5V8zm11 1h2a2 2 0 010 4h-2M8 3v2m3-2v2"/></svg></div>
          <h3>Made to order</h3>
          <p>Hot, iced, creamy or fruity, each drink is made when you order it.</p>
        </div>
        <div class="svc">
          <div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFDF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h11M4 17h6"/></svg></div>
          <h3>Pick your size</h3>
          <p>Choose the size that suits you{{ $lowestPrice ? ', from ₱'.$lowestPrice : '' }}.</p>
        </div>
        <div class="svc">
          <div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFDF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm0 3h18"/></svg></div>
          <h3>Pay your way</h3>
          <p>Cash, GCash or card, whichever is easiest for you.</p>
        </div>
        <div class="svc">
          <div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFDF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10l1.5-5h15L21 10M5 10v9h14v-9M9 19v-5h6v5"/></svg></div>
          <h3>Order at the counter</h3>
          <p>Walk up, tell us what you feel like, and we take it from there.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ VISIT ============ -->
  <section class="sec foam" id="visit">
    <div class="wrap">
      <div class="kicker">Visit</div>
      <h2>Come say hi.</h2>
      @if ($address || $hours || $contact)
      <div class="info">
        @if ($address)
        <div class="svc">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11zm0-8.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
          <h3>Where</h3><p>{{ $address }}</p>
        </div>
        @endif
        @if ($hours)
        <div class="svc">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v5l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h3>Open</h3><p>{{ $hours }}</p>
        </div>
        @endif
        @if ($contact)
        <div class="svc">
          <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#1A3B52" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5a2 2 0 012-2h2.3a1 1 0 01.95.68l1 3a1 1 0 01-.5 1.2l-1.5.8a11 11 0 005.07 5.07l.8-1.5a1 1 0 011.2-.5l3 1a1 1 0 01.68.95V19a2 2 0 01-2 2A16 16 0 013 5z"/></svg>
          <h3>Say hello</h3><p>{{ $contact }}</p>
        </div>
        @endif
      </div>
      @else
      <p class="sub">Our address and opening hours are coming soon.</p>
      @endif
      <footer>
        <span class="brand" style="font-size:20px">Catbrews</span>
        <nav aria-label="Footer"><a href="#home">Home</a><a href="#about">About</a><a href="#menu">Menu</a><a href="#services">Services</a><a href="{{ route('admin.login') }}">Login</a></nav>
      </footer>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(function () {
  var root = document.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function fallback() { root.classList.add('no-gl'); }
  if (!window.THREE) { fallback(); return; }

  var renderer;
  try { renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('gl'), antialias: true, alpha: true }); }
  catch (e) { fallback(); return; }
  renderer.setClearColor(0x000000, 0);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(32, 1, 0.1, 100);
  camera.position.set(0, 0.5, 9.5); camera.lookAt(0, 0, 0);

  scene.add(new THREE.HemisphereLight(0xffffff, 0xd9c2a6, 0.9));
  var key = new THREE.DirectionalLight(0xffffff, 0.9); key.position.set(4, 6, 6); scene.add(key);
  var rim = new THREE.DirectionalLight(0xfff0dc, 0.5); rim.position.set(-6, 3, -4); scene.add(rim);

  /* ---------- helpers ---------- */
  function clamp01(x) { return Math.max(0, Math.min(1, x)); }
  function seg(p, a, b) { return clamp01((p - a) / (b - a)); }
  function smooth(x) { x = clamp01(x); return x * x * (3 - 2 * x); }
  function mix(a, b, f) { return a + (b - a) * f; }
  function easeOut(x) { return 1 - Math.pow(1 - clamp01(x), 3); }
  var seed = 7;
  function rnd() { seed = (seed * 16807) % 2147483647; return (seed - 1) / 2147483646; }

  /* cup geometry: a tapered clear cup, 3 units tall */
  var YB = -1.42, YT = 1.48, RB = 0.89, RT = 1.24;
  function rIn(y) { return RB + (y - YB) / (YT - YB) * (RT - RB); }
  var FILL_END = 1.1, FOAM = 0.34;

  /* ---------- procedural textures ---------- */
  function chocolateTexture() {
    var W = 1024, H = 512, c = document.createElement('canvas'); c.width = W; c.height = H;
    var x = c.getContext('2d');
    for (var i = 0; i < 56; i++) {
      var cx = 40 + rnd() * (W - 80), w = i < 34 ? 7 + rnd() * 24 : 3 + rnd() * 4, len = 150 + rnd() * 330;
      var wob = rnd() * 6;
      function half(y) { return (w / 2) * (1 - Math.pow(y / len, 2.2) * 0.8); }
      x.beginPath(); x.moveTo(cx - half(0), 0);
      for (var y = 0; y <= len; y += 10) x.lineTo(cx - half(y) + Math.sin(y * 0.045 + cx) * wob, y);
      x.quadraticCurveTo(cx, len + w * 0.9, cx + half(len), len);
      for (var y2 = len; y2 >= 0; y2 -= 10) x.lineTo(cx + half(y2) + Math.sin(y2 * 0.045 + cx) * wob, y2);
      x.closePath();
      var gg = x.createLinearGradient(0, 0, 0, len);
      gg.addColorStop(0, 'rgba(36,19,10,.97)'); gg.addColorStop(1, 'rgba(78,46,28,.88)');
      x.fillStyle = gg; x.fill();
    }
    var t = new THREE.CanvasTexture(c); t.wrapS = THREE.RepeatWrapping; t.anisotropy = 4; return t;
  }
  function milkTexture() {
    var W = 1024, H = 512, c = document.createElement('canvas'); c.width = W; c.height = H;
    var x = c.getContext('2d');
    var g = x.createLinearGradient(0, 0, 0, H);
    g.addColorStop(0, '#d6a66c'); g.addColorStop(0.55, '#b9824d'); g.addColorStop(1, '#6f4226');
    x.fillStyle = g; x.fillRect(0, 0, W, H);
    try { x.filter = 'blur(9px)'; } catch (e) {}
    for (var i = 0; i < 70; i++) {
      x.fillStyle = 'rgba(58,31,17,' + (0.1 + rnd() * 0.2) + ')';
      x.beginPath(); x.ellipse(rnd() * W, rnd() * H, 8 + rnd() * 22, 40 + rnd() * 120, 0, 0, 6.283); x.fill();
    }
    for (var j = 0; j < 40; j++) {
      x.fillStyle = 'rgba(244,214,174,' + (0.08 + rnd() * 0.14) + ')';
      x.beginPath(); x.ellipse(rnd() * W, rnd() * H, 10 + rnd() * 26, 30 + rnd() * 90, 0, 0, 6.283); x.fill();
    }
    var t = new THREE.CanvasTexture(c); t.wrapS = THREE.RepeatWrapping; t.anisotropy = 4; return t;
  }
  function foamTexture() {
    var S = 512, c = document.createElement('canvas'); c.width = c.height = S;
    var x = c.getContext('2d');
    var g = x.createRadialGradient(S / 2, S / 2, 20, S / 2, S / 2, S * 0.7);
    g.addColorStop(0, '#ecd1a6'); g.addColorStop(1, '#d4a46a');
    x.fillStyle = g; x.fillRect(0, 0, S, S);
    for (var i = 0; i < 1100; i++) {
      x.fillStyle = rnd() > 0.5 ? 'rgba(176,126,74,.45)' : 'rgba(255,238,212,.55)';
      x.beginPath(); x.arc(rnd() * S, rnd() * S, 1 + rnd() * 4, 0, 6.283); x.fill();
    }
    var t = new THREE.CanvasTexture(c); t.wrapS = t.wrapT = THREE.RepeatWrapping; t.anisotropy = 4; return t;
  }
  function decalTexture() {
    var S = 512, c = document.createElement('canvas'); c.width = c.height = S;
    var x = c.getContext('2d');
    x.strokeStyle = '#2F6690'; x.fillStyle = '#2F6690'; x.lineCap = 'round'; x.lineJoin = 'round';
    x.lineWidth = 11; x.beginPath(); x.arc(S / 2, S / 2, 212, 0, 6.283); x.stroke();
    x.lineWidth = 4; x.beginPath(); x.arc(S / 2, S / 2, 192, 0, 6.283); x.stroke();
    x.save(); x.translate(S / 2 - 32 * 4.3, 200 - 28 * 4.3); x.scale(4.3, 4.3); x.lineWidth = 3.2;
    ['M20 24 L16 10 L28 20', 'M44 24 L48 10 L36 20', 'M24 16 Q32 8 40 16', 'M30 40 Q32 42 34 40'].forEach(function (d) { x.stroke(new Path2D(d)); });
    x.beginPath(); x.arc(32, 8, 3.2, 0, 6.283); x.stroke();
    x.beginPath(); x.arc(32, 36, 17, 0, 6.283); x.stroke();
    x.beginPath(); x.arc(26, 34, 1.7, 0, 6.283); x.fill();
    x.beginPath(); x.arc(38, 34, 1.7, 0, 6.283); x.fill();
    x.restore();
    x.font = '800 44px "Baloo 2", system-ui, sans-serif'; x.textAlign = 'center'; x.fillText('CATBREWS', S / 2, 390);
    var t = new THREE.CanvasTexture(c); t.anisotropy = 4; return t;
  }
  function softTexture(rgb) {
    var c = document.createElement('canvas'); c.width = c.height = 128;
    var x = c.getContext('2d'), g = x.createRadialGradient(64, 64, 4, 64, 64, 62);
    g.addColorStop(0, 'rgba(' + rgb + ',.55)'); g.addColorStop(1, 'rgba(' + rgb + ',0)');
    x.fillStyle = g; x.fillRect(0, 0, 128, 128);
    return new THREE.CanvasTexture(c);
  }

  /* ---------- the cup ---------- */
  var cupRoot = new THREE.Group();
  var tilt = new THREE.Group();
  var spin = new THREE.Group();
  cupRoot.add(tilt); tilt.add(spin); scene.add(cupRoot);

  /* soft shadow on the table */
  var shadow = new THREE.Mesh(new THREE.PlaneGeometry(5.2, 5.2), new THREE.MeshBasicMaterial({ map: softTexture('48,28,14'), transparent: true, depthWrite: false }));
  shadow.rotation.x = -Math.PI / 2; shadow.position.y = -1.58; cupRoot.add(shadow);

  /* liquid: espresso and milk */
  var milkTex = milkTexture(), chocTex = chocolateTexture(), foamTex = foamTexture();
  var VERT = 'varying vec2 vUv; varying float vY; varying vec3 vN; varying vec3 vV;' +
    'void main(){ vUv=uv; vY=position.y+OFFSET; vec4 mv=modelViewMatrix*vec4(position,1.0); vN=normalize(normalMatrix*normal); vV=normalize(-mv.xyz); gl_Position=projectionMatrix*mv; }';
  var liquidMat = new THREE.ShaderMaterial({
    side: THREE.DoubleSide,
    uniforms: { uMap: { value: milkTex }, uFill: { value: -2 }, uTime: { value: 0 } },
    vertexShader: VERT.replace('OFFSET', '0.03'),
    fragmentShader: 'uniform sampler2D uMap; uniform float uFill; uniform float uTime; varying vec2 vUv; varying float vY; varying vec3 vN; varying vec3 vV;' +
      'void main(){ if(vY>uFill) discard; vec2 uv=vUv; uv.x+=uTime*0.012; vec3 c=texture2D(uMap,uv).rgb;' +
      ' float fres=pow(1.0-abs(dot(normalize(vN),normalize(vV))),2.0); c=c*(0.84+0.2*(1.0-fres))+vec3(0.16,0.1,0.05)*fres; gl_FragColor=vec4(c,1.0); }'
  });
  var liquidWall = new THREE.Mesh(new THREE.CylinderGeometry(rIn(1.46) - 0.02, rIn(-1.4) - 0.02, 2.86, 64, 1, true), liquidMat);
  liquidWall.position.y = 0.03; spin.add(liquidWall);

  var liquidBottom = new THREE.Mesh(new THREE.CircleGeometry(1, 48), new THREE.MeshStandardMaterial({ color: 0x5a331c, roughness: 0.6 }));
  liquidBottom.rotation.x = -Math.PI / 2; liquidBottom.position.y = -1.4; liquidBottom.scale.setScalar(rIn(-1.4) - 0.02); spin.add(liquidBottom);

  var liquidCap = new THREE.Mesh(new THREE.CircleGeometry(1, 64), new THREE.MeshStandardMaterial({ color: 0xb5804c, roughness: 0.3 }));
  liquidCap.rotation.x = -Math.PI / 2; spin.add(liquidCap);

  /* chocolate sauce clinging to the wall */
  var chocMat = new THREE.ShaderMaterial({
    side: THREE.DoubleSide, transparent: true, depthWrite: false,
    uniforms: { uMap: { value: chocTex }, uReveal: { value: 0 } },
    vertexShader: 'varying vec2 vUv; void main(){ vUv=uv; gl_Position=projectionMatrix*modelViewMatrix*vec4(position,1.0); }',
    fragmentShader: 'uniform sampler2D uMap; uniform float uReveal; varying vec2 vUv;' +
      'float h(float x){ return fract(sin(x*127.1)*43758.5453); }' +
      'void main(){ float top=1.0-vUv.y; float reach=uReveal*(1.25+0.3*h(floor(vUv.x*48.0)))-0.02;' +
      ' vec4 t=texture2D(uMap,vUv); float m=1.0-smoothstep(reach-0.05,reach,top); gl_FragColor=vec4(t.rgb,t.a*m); }'
  });
  var chocWall = new THREE.Mesh(new THREE.CylinderGeometry(rIn(1.46) - 0.005, rIn(-1.4) - 0.005, 2.86, 64, 1, true), chocMat);
  chocWall.position.y = 0.03; chocWall.renderOrder = 2; spin.add(chocWall);

  /* foam */
  var foamMat = new THREE.MeshStandardMaterial({ map: foamTex, roughness: 0.75 });
  var foamWall = new THREE.Mesh(new THREE.CylinderGeometry(rIn(FILL_END + FOAM) - 0.001, rIn(FILL_END) - 0.001, 1, 64, 1, true), foamMat);
  foamWall.material.side = THREE.DoubleSide; spin.add(foamWall);
  var foamCap = new THREE.Mesh(new THREE.CircleGeometry(1, 64), foamMat);
  foamCap.rotation.x = -Math.PI / 2; foamCap.scale.setScalar(rIn(FILL_END + FOAM) - 0.02); spin.add(foamCap);

  /* ice cubes */
  var iceDefs = [[-0.4, -1.12, 0.2, 0.46], [0.32, -1.08, -0.28, 0.44], [0.04, -0.7, 0.36, 0.42], [-0.42, -0.62, -0.22, 0.4], [0.46, -0.52, 0.12, 0.4]];
  var iceMat = new THREE.MeshStandardMaterial({ color: 0xe6f3fb, transparent: true, opacity: 0.5, roughness: 0.08, depthWrite: false });
  var ice = iceDefs.map(function (d, i) {
    var m = new THREE.Mesh(new THREE.BoxGeometry(d[3], d[3], d[3]), iceMat);
    m.rotation.set(rnd() * 1.2, rnd() * 3, rnd() * 1.2); m.renderOrder = 3; m.userData = { x: d[0], y: d[1], z: d[2], i: i };
    spin.add(m); return m;
  });

  /* clear plastic shell + rims */
  var shellPts = [[0, -1.46], [0.84, -1.46], [0.92, -1.5], [1.29, 1.5], [1.33, 1.55], [1.27, 1.55], [1.245, 1.48], [0.89, -1.42], [0, -1.42]];
  var shell = new THREE.Mesh(
    new THREE.LatheGeometry(shellPts.map(function (p) { return new THREE.Vector2(p[0], p[1]); }), 72),
    new THREE.MeshStandardMaterial({ color: 0xffffff, transparent: true, opacity: 0.14, roughness: 0.05, side: THREE.DoubleSide, depthWrite: false })
  );
  shell.renderOrder = 4; spin.add(shell);
  var rimMat = new THREE.MeshStandardMaterial({ color: 0xffffff, transparent: true, opacity: 0.55, roughness: 0.2, depthWrite: false });
  var rimTop = new THREE.Mesh(new THREE.TorusGeometry(1.3, 0.028, 10, 72), rimMat); rimTop.rotation.x = Math.PI / 2; rimTop.position.y = 1.52; rimTop.renderOrder = 4; spin.add(rimTop);
  var rimBot = new THREE.Mesh(new THREE.TorusGeometry(0.9, 0.03, 10, 64), rimMat); rimBot.rotation.x = Math.PI / 2; rimBot.position.y = -1.47; rimBot.renderOrder = 4; spin.add(rimBot);

  /* printed logo on the front of the cup */
  var yC = -0.15, hD = 1.5, lenD = 1.35;
  var decal = new THREE.Mesh(
    new THREE.CylinderGeometry(rIn(yC + hD / 2) + 0.045, rIn(yC - hD / 2) + 0.045, hD, 48, 1, true, -lenD / 2, lenD),
    new THREE.MeshBasicMaterial({ map: decalTexture(), transparent: true, opacity: 0.9, depthWrite: false })
  );
  decal.position.y = yC; decal.renderOrder = 5; spin.add(decal);

  /* fixed glossy reflections (do not turn with the cup) */
  var glossTex = (function () {
    var c = document.createElement('canvas'); c.width = 64; c.height = 256;
    var x = c.getContext('2d'), g = x.createLinearGradient(0, 0, 64, 0);
    g.addColorStop(0, 'rgba(255,255,255,0)'); g.addColorStop(0.5, 'rgba(255,255,255,.85)'); g.addColorStop(1, 'rgba(255,255,255,0)');
    x.fillStyle = g; x.fillRect(0, 0, 64, 256);
    var v = x.createLinearGradient(0, 0, 0, 256);
    v.addColorStop(0, 'rgba(0,0,0,0)'); v.addColorStop(0.5, 'rgba(0,0,0,1)'); v.addColorStop(1, 'rgba(0,0,0,0)');
    x.globalCompositeOperation = 'destination-in'; x.fillStyle = v; x.fillRect(0, 0, 64, 256);
    return new THREE.CanvasTexture(c);
  })();
  function gloss(theta, len, op) {
    var m = new THREE.Mesh(
      new THREE.CylinderGeometry(rIn(1.3) + 0.07, rIn(-1.3) + 0.07, 2.6, 24, 1, true, theta, len),
      new THREE.MeshBasicMaterial({ map: glossTex, transparent: true, opacity: op, blending: THREE.AdditiveBlending, depthWrite: false })
    );
    m.renderOrder = 6; tilt.add(m); return m;
  }
  gloss(-0.95, 0.2, 0.55); gloss(0.78, 0.1, 0.35);

  /* lid */
  var lidPts = [[0, 0.1], [1.05, 0.1], [1.12, 0.14], [1.3, 0.14], [1.37, 0.1], [1.43, 0.02], [1.43, -0.2], [1.35, -0.2], [1.35, -0.04], [1.2, 0], [0, 0]];
  var lid = new THREE.Group();
  lid.add(new THREE.Mesh(
    new THREE.LatheGeometry(lidPts.map(function (p) { return new THREE.Vector2(p[0], p[1]); }), 72),
    new THREE.MeshStandardMaterial({ color: 0xffffff, transparent: true, opacity: 0.32, roughness: 0.1, side: THREE.DoubleSide, depthWrite: false })
  ));
  var lidRing = new THREE.Mesh(new THREE.TorusGeometry(0.55, 0.018, 8, 56), rimMat); lidRing.rotation.x = Math.PI / 2; lidRing.position.y = 0.11; lid.add(lidRing);
  var lidRing2 = new THREE.Mesh(new THREE.TorusGeometry(1.2, 0.018, 8, 64), rimMat); lidRing2.rotation.x = Math.PI / 2; lidRing2.position.y = 0.12; lid.add(lidRing2);
  lid.children.forEach(function (m) { m.renderOrder = 7; });
  lid.visible = false; spin.add(lid);

  /* pour streams */
  var chocStream = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.05, 1, 14), new THREE.MeshStandardMaterial({ color: 0x2b170c, roughness: 0.25 }));
  var milkStream = new THREE.Mesh(new THREE.CylinderGeometry(0.11, 0.11, 1, 18), new THREE.MeshStandardMaterial({ color: 0xc08a54, roughness: 0.3 }));
  chocStream.visible = milkStream.visible = false; spin.add(chocStream); spin.add(milkStream);

  /* ---------- layout, scroll and steps ---------- */
  var vw = 1, vh = 1, portrait = false;
  var stageEl = document.getElementById('stage'), recipeEl = document.getElementById('home');
  function measure() {
    vw = stageEl.clientWidth || window.innerWidth; vh = stageEl.clientHeight || window.innerHeight;
    renderer.setSize(vw, vh, false);
    var aspect = vw / vh; portrait = aspect < 0.9;
    camera.aspect = aspect; camera.fov = portrait ? 46 : 32;
    camera.position.set(0, portrait ? 0.4 : 0.5, 9.5); camera.lookAt(0, 0, 0);
    camera.updateProjectionMatrix();
  }
  function progress() {
    var r = recipeEl.getBoundingClientRect();
    return clamp01(-r.top / Math.max(1, r.height - vh));
  }

  var STEPS = [
    { k: 'Iced Mocha Latte', t: 'Watch it come together.', d: 'Scroll to make one, layer by layer.' },
    { k: 'Step 1 of 5', t: 'Ice into a clear cup.', d: 'Cold first, so every sip stays cold.' },
    { k: 'Step 2 of 5', t: 'Chocolate goes in first.', d: 'Dark sauce runs down the inside of the cup.' },
    { k: 'Step 3 of 5', t: 'Espresso, then cold milk.', d: 'They pour over the chocolate and swirl together.' },
    { k: 'Step 4 of 5', t: 'A creamy foam on top.', d: 'Soft and sweet, with a little caramel on the surface.' },
    { k: 'Step 5 of 5', t: 'Lid on. Ready.', d: 'Made to order at the Catbrews counter.' }
  ];
  var THRESH = [0, 0.1, 0.3, 0.5, 0.72, 0.84];
  var ctaEl = document.getElementById('cta'), copyEl = document.getElementById('copy'), kEl = document.getElementById('kicker'), tEl = document.getElementById('title'), dEl = document.getElementById('desc');
  var railItems = Array.prototype.slice.call(document.querySelectorAll('.rail li'));
  var hint = document.getElementById('hint');
  var stepNow = 0, swapTimer = null;
  function setStep(n) {
    if (n === stepNow) return;
    stepNow = n;
    railItems.forEach(function (li) {
      var i = +li.getAttribute('data-i');
      li.classList.toggle('on', i === n); li.classList.toggle('done', i < n);
    });
    copyEl.classList.add('out');
    clearTimeout(swapTimer);
    swapTimer = setTimeout(function () {
      kEl.textContent = STEPS[n].k; tEl.textContent = STEPS[n].t; dEl.textContent = STEPS[n].d; ctaEl.hidden = n !== 5;
      copyEl.classList.remove('out');
    }, reduce ? 0 : 260);
  }

  var BG0 = new THREE.Color('#F1E6D8'), BG1 = new THREE.Color('#E4C7A1'), bgTmp = new THREE.Color(), lastBg = '';
  var p = 0, pointer = { x: 0, y: 0, tx: 0, ty: 0 }, clock = new THREE.Clock();
  window.addEventListener('pointermove', function (e) { pointer.tx = (e.clientX / vw - 0.5) * 2; pointer.ty = (e.clientY / vh - 0.5) * 2; }, { passive: true });

  function frame() {
    if (recipeEl.getBoundingClientRect().bottom < 0) { if (!reduce) requestAnimationFrame(frame); return; }
    var t = reduce ? 0 : clock.getElapsedTime();
    var target = progress();
    p = reduce ? target : p + (target - p) * 0.1;
    pointer.x += (pointer.tx - pointer.x) * 0.05; pointer.y += (pointer.ty - pointer.y) * 0.05;

    var iceQ = seg(p, 0.1, 0.28), chocQ = easeOut(seg(p, 0.3, 0.5)), fillQ = smooth(seg(p, 0.5, 0.72)),
        foamQ = smooth(seg(p, 0.72, 0.84)), lidQ = easeOut(seg(p, 0.84, 0.96));

    /* placement */
    var halfW = Math.tan(camera.fov * Math.PI / 360) * 9.5 * camera.aspect;
    cupRoot.position.set(portrait ? 0 : halfW * 0.27, portrait ? 1.2 : 0, 0);
    cupRoot.scale.setScalar(portrait ? 0.92 : Math.max(0.7, Math.min(1, halfW / 4.6)));
    tilt.rotation.x = 0.16 + pointer.y * 0.05;
    tilt.rotation.z = -pointer.x * 0.05;
    spin.rotation.y = t * 0.3 + p * 5.2 + pointer.x * 0.15;

    /* liquid level, foam, chocolate */
    var fillY = mix(-1.4, FILL_END, fillQ);
    liquidMat.uniforms.uFill.value = fillQ > 0.001 ? fillY : -2;
    liquidMat.uniforms.uTime.value = t;
    chocMat.uniforms.uReveal.value = chocQ;
    chocWall.visible = chocQ > 0.001;
    liquidCap.visible = fillQ > 0.001 && foamQ < 0.999;
    liquidCap.position.y = fillY;
    liquidCap.scale.setScalar(Math.max(0.01, rIn(fillY) - 0.02));
    liquidBottom.visible = fillQ > 0.001;

    var foamH = Math.max(0.001, FOAM * foamQ);
    foamWall.visible = foamCap.visible = foamQ > 0.001;
    foamWall.scale.y = foamH; foamWall.position.y = FILL_END + foamH / 2;
    foamCap.position.y = FILL_END + foamH;

    /* ice drops in, then floats up with the milk */
    ice.forEach(function (m) {
      var d = m.userData, q = easeOut(seg(p, 0.1 + d.i * 0.025, 0.2 + d.i * 0.025 + 0.06));
      m.visible = q > 0.001;
      var rest = d.y + Math.max(0, fillY - d.y - 0.35) * 0.75;
      var bob = fillQ > 0.4 ? Math.sin(t * 1.2 + d.i * 1.7) * 0.03 : 0;
      m.position.set(d.x, mix(3.4, Math.min(rest, 0.95), q) + bob, d.z);
      m.rotation.y += reduce ? 0 : 0.002;
    });

    /* streams */
    var chocPour = p > 0.3 && p < 0.5;
    chocStream.visible = chocPour;
    if (chocPour) {
      var top = 3.2, bottom = 1.3, wob = Math.sin(t * 18) * 0.008;
      chocStream.scale.set(1 + wob * 10, top - bottom, 1 + wob * 10); chocStream.position.set(0.98, (top + bottom) / 2, 0.05);
    }
    var milkPour = p > 0.5 && p < 0.725;
    milkStream.visible = milkPour;
    if (milkPour) {
      var mtop = 3.2, mb = fillY + 0.02, shrink = 1 - smooth(seg(p, 0.7, 0.725)), grow = smooth(seg(p, 0.5, 0.52)), w = 0.9 + Math.sin(t * 22) * 0.08;
      milkStream.scale.set(w * shrink * grow + 0.001, mtop - mb, w * shrink * grow + 0.001);
      milkStream.position.set(0, (mtop + mb) / 2, 0);
    }

    /* lid settles on the cup */
    lid.visible = lidQ > 0.001;
    lid.position.y = mix(3.4, 1.5, lidQ);
    lid.rotation.z = (1 - lidQ) * 0.22;

    /* page color warms as the cup fills */
    bgTmp.copy(BG0).lerp(BG1, smooth(seg(p, 0.25, 0.85)));
    var hex = '#' + bgTmp.getHexString();
    if (hex !== lastBg) { root.style.setProperty('--bg', hex); lastBg = hex; }

    /* copy + rail */
    var n = 0; for (var i = 0; i < THRESH.length; i++) if (p >= THRESH[i]) n = i;
    setStep(n);
    hint.classList.toggle('gone', p > 0.03);

    renderer.render(scene, camera);
    if (!reduce) requestAnimationFrame(frame);
  }

    measure();
  window.addEventListener('resize', function () { measure(); if (reduce) frame(); });
  window.addEventListener('load', function () { measure(); if (reduce) frame(); });
  if (reduce) window.addEventListener('scroll', frame, { passive: true });
  frame();
})();



/* ---------- page chrome: nav, tabs ---------- */
(function () {
  var nav = document.getElementById('nav'), burger = document.getElementById('burger');
  burger.addEventListener('click', function () {
    var open = nav.classList.toggle('open');
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.getElementById('links').addEventListener('click', function () { nav.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); });

  var ids = ['home', 'about', 'menu', 'services', 'visit'];
  var links = {}; ids.forEach(function (id) { links[id] = document.querySelector('.links a[data-s="' + id + '"]'); });
  function spy() {
    var y = window.scrollY + window.innerHeight * 0.35, cur = 'home';
    ids.forEach(function (id) { var el = document.getElementById(id); if (el && el.offsetTop <= y) cur = id; });
    ids.forEach(function (id) { links[id].classList.toggle('on', id === cur); });
  }
  window.addEventListener('scroll', spy, { passive: true }); spy();

  var tabs = Array.prototype.slice.call(document.querySelectorAll('.tab'));
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        document.getElementById(t.getAttribute('data-p')).hidden = !on;
      });
    });
  });
})();
</script>
</body>
</html>

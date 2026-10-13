<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Platform overview') · {{ $platformName }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--blue:#2F6690;--ink:#1A3B52;--cream:#FFFDF9;--coral:#E0776B;--muted:#68869d}*{box-sizing:border-box}body{margin:0;background:var(--cream);color:var(--ink);font:15px Nunito,system-ui,sans-serif}a{color:var(--blue)}button,input,select,textarea{font:inherit}button,a,input,select,textarea{outline-offset:4px}h1,h2,h3{font-family:'Baloo 2',system-ui;margin:0;line-height:1.2}h1{font-size:36px}h2{font-size:23px}p{line-height:1.6}aside{position:fixed;inset:20px auto 20px 20px;width:235px;display:flex;flex-direction:column;padding:28px 20px;background:white;border:1px solid #d3e6f3;border-radius:30px;box-shadow:0 12px 26px #1a3b5210;overflow-y:auto}.brand{font-size:25px;font-weight:800}.eyebrow{font-size:11px;letter-spacing:2px;color:var(--muted);font-weight:800;margin:8px 0 28px}.nav{display:grid;gap:9px}.nav a{padding:13px 15px;border-radius:15px;text-decoration:none;font-weight:800}.nav a:hover{background:#FBF3E4}.nav a.current{background:var(--blue);color:white;box-shadow:0 4px 0 var(--ink)}.account{margin-top:auto;padding-top:28px}.account button{width:100%}main{margin-left:275px;padding:38px 30px;max-width:1700px}.heading{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:27px}.muted{color:var(--muted)}.subtitle{margin:8px 0 0;color:var(--muted)}.card{background:white;padding:26px;border:1px solid #e0eaf1;border-radius:28px;box-shadow:0 14px 28px #1a3b5210;margin-bottom:22px}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}.stats .card{padding:23px}.stat-label{font-size:12px;font-weight:800;color:var(--muted)}.stat-number{font-family:'Baloo 2';font-size:35px;font-weight:800;margin:8px 0 0}.split{display:grid;grid-template-columns:minmax(0,1.8fr) minmax(260px,1fr);gap:22px}.btn{display:inline-flex;justify-content:center;align-items:center;gap:8px;border:0;border-radius:13px;padding:11px 17px;background:var(--blue);color:white;text-decoration:none;font-weight:800;cursor:pointer}.btn:hover{filter:brightness(.94)}.btn.secondary{background:#EAF3FA;color:var(--blue)}.btn.danger{background:#FBEDEB;color:#9C4A41}.badge{display:inline-block;padding:5px 10px;background:#EAF3FA;color:var(--blue);border-radius:20px;font-size:12px;font-weight:800;white-space:nowrap}.badge.active{background:#EAF5EF;color:#3F7457}.badge.trial{background:#FBF3E4;color:#866426}.badge.suspended,.badge.past_due,.badge.cancelled{background:#FBEDEB;color:#9C4A41}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:620px}th{text-align:left;font-size:12px;color:var(--muted);background:#f5f8fa}th,td{padding:15px 12px;border-bottom:1px solid #edf1f4}td{font-weight:700}.stack{display:grid;gap:18px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.field label{display:block;font-weight:800;font-size:13px;margin-bottom:7px}input:not([type=checkbox]),select,textarea{width:100%;border:1px solid #d3e6f3;background:#fffdf9;border-radius:12px;padding:11px 13px;color:var(--ink)}textarea{min-height:95px;resize:vertical}.toolbar{display:flex;gap:12px;align-items:center;margin:20px 0;flex-wrap:wrap}.toolbar input{max-width:300px}.toolbar select{max-width:180px}.notice{padding:14px 18px;border-radius:15px;background:#EAF5EF;color:#3F7457;margin-bottom:20px}.errors{background:#FBEDEB;color:#9C4A41}.activity-row{padding:15px 0;border-bottom:1px solid #edf1f4}.activity-row:last-child{border:0}.tiny{font-size:12px}.pagination{display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:15px}.pagination a{font-weight:800}.mobile-nav{display:none}.full{grid-column:1/-1}summary{cursor:pointer;font-weight:800}.check{display:flex;align-items:center;gap:9px}button:disabled{opacity:.5;cursor:default}
    .split table{min-width:0}.split th,.split td{padding:14px 8px;font-size:13px}.split .btn{padding:9px 12px}
    @media(max-width:1100px){.stats{grid-template-columns:repeat(2,1fr)}.split{grid-template-columns:1fr}}
    @media(max-width:760px){aside{display:none}main{margin:0;padding:24px 16px}.mobile-nav{display:block;background:white;padding:16px;border-bottom:1px solid #e0eaf1}.mobile-nav .nav{margin-top:15px}.heading{align-items:flex-start;flex-wrap:wrap}h1{font-size:30px}.grid{grid-template-columns:1fr}.card{padding:20px}.stats{gap:10px}.stats .card{padding:16px}.stat-number{font-size:28px}}
  </style>
</head>
<body>
  @php($navigation = ['dashboard' => 'Overview', 'shops.index' => 'Shops', 'subscriptions' => 'Subscriptions', 'plans.index' => 'Plans', 'users.index' => 'Users & Roles', 'activity' => 'System Activity', 'emails' => 'Email Delivery', 'settings' => 'Settings'])
  <aside aria-label="Superadmin navigation">
    <div class="brand">{{ $platformName }}</div><div class="eyebrow">SUPERADMIN</div>
    <nav class="nav">
      @foreach($navigation as $key => $label)
        <a href="{{ route('superadmin.'.$key) }}" class="{{ request()->routeIs('superadmin.'.$key) || ($key === 'shops.index' && request()->routeIs('superadmin.shops.*')) ? 'current' : '' }}">{{ $label }}</a>
      @endforeach
    </nav>
    <div class="account"><p><strong>{{ auth()->user()->name }}</strong><br><span class="muted tiny">Platform administrator</span></p><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="btn secondary">Sign out</button></form></div>
  </aside>
  <details class="mobile-nav"><summary>{{ $platformName }} · Superadmin menu</summary><nav class="nav">@foreach($navigation as $key => $label)<a href="{{ route('superadmin.'.$key) }}">{{ $label }}</a>@endforeach</nav><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="btn secondary">Sign out</button></form></details>
  <main>
    @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice errors" role="alert"><strong>Please check these details:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
  </main>
</body>
</html>

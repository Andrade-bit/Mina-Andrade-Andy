<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews Transactions</title>
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

  <header class="bg-white shadow-soft-sm px-5 py-3 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ route('pos.terminal') }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Back to POS">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <p class="font-display font-bold text-stamp-700 leading-tight">Transactions</p>
        <p class="text-[11px] text-stamp-300 font-bold">Owner/Admin view</p>
      </div>
    </div>
    <a href="{{ route('admin.users') }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Admin dashboard">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    </a>
  </header>

  <main class="p-5 md:p-8 max-w-6xl mx-auto">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Today's Sales</p>
        <p id="statSales" class="font-display font-bold text-2xl text-stamp-700">₱0.00</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Transactions</p>
        <p id="statCount" class="font-display font-bold text-2xl text-stamp-700">0</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">By Owner</p>
        <p id="statOwner" class="font-display font-bold text-2xl text-stamp-700">0</p>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">By Staff</p>
        <p id="statStaff" class="font-display font-bold text-2xl text-stamp-700">0</p>
      </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 overflow-x-auto">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-4">Sales Log</h3>
      <table class="w-full min-w-[720px] text-left border-collapse">
        <thead>
          <tr class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">
            <th class="py-2 px-4">Transaction ID</th>
            <th class="py-2 px-4">Date/Time</th>
            <th class="py-2 px-4">Items</th>
            <th class="py-2 px-4">Payment</th>
            <th class="py-2 px-4">Processed By</th>
            <th class="py-2 px-4">Total</th>
            <th class="py-2 px-4">Actions</th>
          </tr>
        </thead>
        <tbody id="txnBody">
          <tr class="border-b border-cream-200">
            <td class="py-3 px-4 font-bold text-stamp-700 text-sm">CB-20260917-0478</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">Sep 17, 2026, 8:14 AM</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">2x Cafe Latte, 1x Mango Juice</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">Cash</td>
            <td class="py-3 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-mint-50 text-mint-600">Juan Dela Cruz</span></td>
            <td class="py-3 px-4 font-display font-bold text-stamp-700">₱277.00</td>
            <td class="py-3 px-4"><button class="text-coral-500 text-xs font-bold hover:underline">Void</button></td>
          </tr>
          <tr class="border-b border-cream-200">
            <td class="py-3 px-4 font-bold text-stamp-700 text-sm">CB-20260917-0479</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">Sep 17, 2026, 9:02 AM</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">1x Spanish Latte, 1x Lemonade</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">GCash</td>
            <td class="py-3 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-stamp-500 text-cream-50">Admin</span></td>
            <td class="py-3 px-4 font-display font-bold text-stamp-700">₱178.00</td>
            <td class="py-3 px-4"><button class="text-coral-500 text-xs font-bold hover:underline">Void</button></td>
          </tr>
          <tr class="border-b border-cream-200 last:border-0">
            <td class="py-3 px-4 font-bold text-stamp-700 text-sm">CB-20260917-0480</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">Sep 17, 2026, 9:47 AM</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">3x Matcha Latte</td>
            <td class="py-3 px-4 text-stamp-500 text-sm">Card</td>
            <td class="py-3 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-mint-50 text-mint-600">Juan Dela Cruz</span></td>
            <td class="py-3 px-4 font-display font-bold text-stamp-700">₱327.00</td>
            <td class="py-3 px-4"><button class="text-coral-500 text-xs font-bold hover:underline">Void</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script>
    const seedTotals = { sales: 782, count: 3, owner: 1, staff: 2 };

    document.addEventListener('DOMContentLoaded', () => {
      const txns = JSON.parse(localStorage.getItem('cb_transactions') || '[]');
      const tbody = document.getElementById('txnBody');

      let sales = seedTotals.sales, count = seedTotals.count, owner = seedTotals.owner, staff = seedTotals.staff;

      txns.slice().reverse().forEach(t => {
        const row = document.createElement('tr');
        row.className = 'border-b border-cream-200';
        const badgeClass = t.processedByRole === 'Owner' ? 'bg-stamp-500 text-cream-50' : 'bg-mint-50 text-mint-600';
        row.innerHTML = `
          <td class="py-3 px-4 font-bold text-stamp-700 text-sm">${t.id}</td>
          <td class="py-3 px-4 text-stamp-500 text-sm">${t.datetime}</td>
          <td class="py-3 px-4 text-stamp-500 text-sm">${t.items.map(i => i.qty + 'x ' + i.name).join(', ')}</td>
          <td class="py-3 px-4 text-stamp-500 text-sm">${t.payment}</td>
          <td class="py-3 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ${badgeClass}">${t.processedBy}</span></td>
          <td class="py-3 px-4 font-display font-bold text-stamp-700">₱${t.total.toFixed(2)}</td>
          <td class="py-3 px-4"><button class="text-coral-500 text-xs font-bold hover:underline">Void</button></td>
        `;
        tbody.prepend(row);

        sales += t.total;
        count += 1;
        if (t.processedByRole === 'Owner') owner += 1; else staff += 1;
      });

      document.getElementById('statSales').textContent = '₱' + sales.toFixed(2);
      document.getElementById('statCount').textContent = count;
      document.getElementById('statOwner').textContent = owner;
      document.getElementById('statStaff').textContent = staff;
    });
  </script>

</body>
</html>

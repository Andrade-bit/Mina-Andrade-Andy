@php
  // An admin signed in to the dashboard sees Orders inside the admin layout. Anyone else (an admin PIN holder
  // on the POS) keeps the standalone POS-style page.
  $inAdmin = auth()->check() && ! session('dashboard_locked');
@endphp
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
<body class="min-h-screen bg-cream-50 font-body text-stamp-700 {{ $inAdmin ? 'flex' : '' }}">

  @php
    $fromTerminal = request('back') === 'terminal';
  @endphp

  @if ($inAdmin)
    @include('admin.partials.sidebar', ['active' => 'orders'])
  @else
  <header class="bg-white shadow-soft-sm px-5 py-3 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ $fromTerminal ? route('pos.terminal') : (auth()->check() ? route('admin.dashboard') : route('pos.terminal')) }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="{{ $fromTerminal || ! auth()->check() ? 'Back to POS' : 'Back to Dashboard' }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <p class="font-display font-bold text-stamp-700 leading-tight">Transactions</p>
        <p class="text-[11px] text-stamp-300 font-bold">Owner/Admin view</p>
      </div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors" title="Admin dashboard">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    </a>
  </header>
  @endif

  <main class="{{ $inAdmin ? 'flex-1 min-w-0 p-5 pt-20 md:p-8 overflow-y-auto' : 'p-5 md:p-8 max-w-6xl mx-auto' }}">

    @if ($inAdmin)
      <div class="mb-6">
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">Orders</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Every sale made at the terminal. Void one here if it was a mistake.</p>
      </div>
    @endif


    @php
      $keepBack = $fromTerminal ? ['back' => 'terminal'] : [];
      $todayFilter = [...$keepBack, 'from' => today()->toDateString(), 'to' => today()->toDateString()];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <a href="{{ route('pos.transactions', $todayFilter) }}" class="bg-white rounded-3xl shadow-soft p-5 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Today's Sales</p>
        <p class="font-display font-bold text-2xl text-stamp-700">₱{{ number_format($todaysSales, 2) }}</p>
      </a>
      <a href="{{ route('pos.transactions', $todayFilter) }}" class="bg-white rounded-3xl shadow-soft p-5 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">Transactions</p>
        <p class="font-display font-bold text-2xl text-stamp-700">{{ $todaysCount }}</p>
      </a>
      <a href="{{ route('pos.transactions', [...$keepBack, 'role' => 'admin']) }}" class="bg-white rounded-3xl shadow-soft p-5 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">By Admin</p>
        <p class="font-display font-bold text-2xl text-stamp-700">{{ $byOwnerCount }}</p>
      </a>
      <a href="{{ route('pos.transactions', [...$keepBack, 'role' => 'assistant']) }}" class="bg-white rounded-3xl shadow-soft p-5 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300">
        <p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300 mb-1">By Staff</p>
        <p class="font-display font-bold text-2xl text-stamp-700">{{ $byStaffCount }}</p>
      </a>
    </div>

    @if (request()->anyFilled(['status', 'role']))
      <div class="mb-4 flex items-center justify-between gap-3 flex-wrap rounded-2xl bg-stamp-50 px-4 py-3 text-sm font-bold text-stamp-600">
        <span>
          Showing
          @if (request('status') === 'voided') voided sales @elseif (request('status') === 'completed') completed sales @else all sales @endif
          @if (request('role') === 'admin') made by admins @elseif (request('role') === 'assistant') made by staff @endif
        </span>
        <a href="{{ route('pos.transactions', $keepBack) }}" class="underline">Clear</a>
      </div>
    @endif

    <form method="GET" action="{{ route('pos.transactions') }}" class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-4 space-y-3">
      @foreach (['status', 'role'] as $kept)
        @if (request()->filled($kept))
          <input type="hidden" name="{{ $kept }}" value="{{ request($kept) }}">
        @endif
      @endforeach
      @if ($fromTerminal)
        <input type="hidden" name="back" value="terminal">
      @endif
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5 flex-1 min-w-[220px]">
          <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # or staff name..." class="w-full bg-transparent outline-none text-sm text-stamp-700 placeholder-stamp-300 font-semibold">
        </div>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5">
          <select name="payment_method" onchange="this.form.submit()" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
            <option value="">All Payments</option>
            <option value="Cash" @selected(request('payment_method') === 'Cash')>Cash</option>
            <option value="GCash" @selected(request('payment_method') === 'GCash')>GCash</option>
            <option value="Card" @selected(request('payment_method') === 'Card')>Card</option>
          </select>
        </div>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5">
          <select name="credential_id" onchange="this.form.submit()" class="bg-transparent outline-none text-sm text-stamp-600 font-semibold">
            <option value="">All Staff</option>
            @foreach ($staffOptions as $staff)
              <option value="{{ $staff->id }}" @selected((int) request('credential_id') === $staff->id)>{{ $staff->first_name }} {{ $staff->last_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5 flex items-center gap-2">
          <span class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">From</span>
          <input type="date" name="from" value="{{ request('from') }}" class="bg-transparent outline-none text-sm text-stamp-700 font-semibold">
        </div>
        <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5 flex items-center gap-2">
          <span class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">To</span>
          <input type="date" name="to" value="{{ request('to') }}" class="bg-transparent outline-none text-sm text-stamp-700 font-semibold">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-stamp-500 hover:bg-stamp-600 text-cream-50 font-extrabold text-xs transition-colors">Filter</button>
        @if (request()->anyFilled(['search', 'payment_method', 'credential_id', 'from', 'to']))
          <a href="{{ route('pos.transactions', $fromTerminal ? ['back' => 'terminal'] : []) }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-500 font-extrabold text-xs transition-colors">Clear</a>
        @endif
      </div>
    </form>

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
            <th class="py-2 px-4">Status</th>
            <th class="py-2 px-4"></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transactions as $transaction)
            <tr class="border-b border-cream-200 last:border-0 {{ $transaction->status === 'voided' ? 'opacity-50' : '' }}">
              <td class="py-3 px-4 font-bold text-stamp-700 text-sm">CB-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $transaction->transaction_date->format('M j, Y, g:i A') }}</td>
              <td class="py-3 px-4 text-stamp-500 text-sm">
                {{ $transaction->items->map(fn ($item) => $item->quantity.'x '.$item->product->product_name)->implode(', ') }}
                @if ($transaction->promo)
                  <div class="mt-1.5 max-w-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-mint-50 text-mint-600">Promo {{ $transaction->promo->code }} &middot; &minus;₱{{ number_format($transaction->discount_amount, 2) }}</span>
                    @if ($transaction->promo->reason)
                      <p class="text-[11px] text-stamp-400 font-semibold italic break-words mt-0.5">&ldquo;{{ $transaction->promo->reason }}&rdquo;</p>
                    @endif
                  </div>
                @endif
                @if ($transaction->status === 'voided' && $transaction->void_reason)
                  <p class="mt-1.5 max-w-xs text-[11px] text-coral-600 font-semibold break-words">Voided: {{ $transaction->void_reason }}</p>
                @endif
              </td>
              <td class="py-3 px-4 text-stamp-500 text-sm">{{ $transaction->payment_method }}</td>
              <td class="py-3 px-4">
                @if ($transaction->credential)
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $transaction->credential->role === 'admin' ? 'bg-stamp-500 text-cream-50' : 'bg-mint-50 text-mint-600' }}">{{ $transaction->credential->first_name }} {{ $transaction->credential->last_name }}</span>
                @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-cream-100 text-stamp-400">Unknown</span>
                @endif
              </td>
              <td class="py-3 px-4 font-display font-bold text-stamp-700">₱{{ number_format($transaction->total_amount, 2) }}</td>
              <td class="py-3 px-4">
                @if ($transaction->status === 'voided')
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-coral-50 text-coral-600" title="{{ $transaction->void_reason }}">Voided</span>
                @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-mint-50 text-mint-600">Completed</span>
                @endif
              </td>
              <td class="py-3 px-4">
                @if ($transaction->status !== 'voided')
                  <button type="button" onclick="openVoidModal({{ $transaction->id }}, {{ Illuminate\Support\Js::from('CB-'.str_pad($transaction->id, 5, '0', STR_PAD_LEFT)) }})" class="text-[11px] font-extrabold text-coral-500 hover:text-coral-600">Void</button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-10 text-center text-sm font-semibold text-stamp-300">
                @if (request()->anyFilled(['search', 'payment_method', 'credential_id', 'from', 'to']))
                  No sales match your filters.
                @else
                  No sales recorded yet.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      @include('admin.partials.pagination', ['paginator' => $transactions])
    </div>
  </main>

  <!-- Void Sale Modal -->
  <div id="voidModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-md p-7 relative max-h-[90vh] overflow-y-auto">
      <button type="button" onclick="closeVoidModal()" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-coral-50 shadow-soft-inset mx-auto flex items-center justify-center text-coral-500 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Void Sale</h3>
        <p id="voidModalLabel" class="text-xs text-stamp-400 font-semibold mt-1">Voiding —</p>
      </div>
      <form id="voidForm" method="POST" action="" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Reason</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <textarea name="void_reason" rows="4" maxlength="500" required oninput="document.getElementById('voidReasonCount').textContent = this.value.length" placeholder="e.g. Wrong item rung up, customer changed their mind" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm resize-none">{{ old('void_reason') }}</textarea>
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold text-right mt-1 mr-1"><span id="voidReasonCount">0</span>/500</p>
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Admin PIN</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <input type="password" inputmode="numeric" maxlength="4" name="admin_pin" placeholder="••••" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm tracking-[0.3em]">
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">This does not restore inventory — it only marks the sale as voided.</p>
        </div>
        <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-b from-coral-500 to-coral-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150">
          Confirm Void
        </button>
      </form>
    </div>
  </div>

  <script>
    function openVoidModal(id, label){
      document.getElementById('voidModalLabel').textContent = 'Voiding ' + label;
      document.getElementById('voidForm').action = '/pos/transactions/' + id + '/void';
      document.getElementById('voidModal').classList.remove('hidden');
    }
    function closeVoidModal(){ document.getElementById('voidModal').classList.add('hidden'); }

  </script>

  @include('admin.partials.toasts')

</body>
</html>

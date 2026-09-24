<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catbrews - User Management</title>
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

  <!-- Mobile top bar -->
  <div class="md:hidden fixed top-0 inset-x-0 z-30 bg-white shadow-soft-sm px-4 py-3 flex items-center gap-3">
    <button onclick="openSidebar()" class="w-10 h-10 rounded-xl bg-cream-100 flex items-center justify-center text-stamp-600 shrink-0">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="font-display font-bold text-stamp-700">Catbrews</p>
  </div>

  <!-- Sidebar backdrop (mobile) -->
  <div id="sidebarBackdrop" onclick="closeSidebar()" class="hidden md:hidden fixed inset-0 bg-stamp-700/40 z-40"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 flex flex-col w-72 md:w-64 bg-white md:bg-white/70 shadow-soft m-0 md:m-4 rounded-none md:rounded-[2rem] p-6 shrink-0 -translate-x-full md:translate-x-0 transition-transform duration-300 overflow-y-auto">
    <button onclick="closeSidebar()" class="md:hidden absolute top-4 right-4 text-stamp-300 hover:text-stamp-600">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="flex flex-col items-center mb-8">
      <div class="w-16 h-16 rounded-full bg-cream-100 shadow-soft-inset ring-4 ring-white flex items-center justify-center text-stamp-600 mb-2">
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
      <h1 class="font-display font-bold text-xl text-stamp-700">Catbrews</h1>
      <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-stamp-300">Admin Panel</p>
    </div>

    <nav class="flex-1 space-y-1.5">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-cream-50 bg-gradient-to-b from-stamp-500 to-stamp-600 shadow-soft-btn">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
        User Management
      </a>
      <a href="{{ route('pos.login') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-9 4h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/></svg>
        POS Terminal
      </a>
      <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Products
      </a>
      <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        Inventory
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5-1a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm11 6.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
        Orders
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Reports
      </a>
      <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-stamp-500 hover:bg-cream-100 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    </nav>

    <div class="pt-4 mt-auto border-t border-cream-200 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 font-display font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="text-sm flex-1">
        <p class="font-bold text-stamp-700 leading-tight">{{ auth()->user()->name }}</p>
        <p class="text-stamp-300 text-xs font-bold">Owner</p>
      </div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" title="Log out" class="text-stamp-300 hover:text-stamp-600">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">User Management</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Create and manage staff accounts for Catbrews</p>
      </div>
      <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-6 py-3.5 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create Staff Account
      </button>
    </div>

    @if (session('status'))
      <div class="mb-5 bg-mint-50 text-mint-600 text-sm font-bold rounded-2xl px-4 py-3">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-5 bg-coral-500/10 text-coral-600 text-sm font-bold rounded-2xl px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total Staff</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $staff->count() }}</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Admins</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $staff->where('role', 'admin')->count() }}</p></div>
      </div>
      <div class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Assistants</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $staff->where('role', 'assistant')->count() }}</p></div>
      </div>
    </div>

    <!-- Staff list -->
    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6">
      <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <h3 class="font-display font-bold text-stamp-700 text-lg">Staff Directory</h3>
        <div class="flex items-center gap-2 bg-cream-100 rounded-2xl shadow-soft-inset px-4 py-2.5 w-full sm:w-64">
          <svg class="w-4 h-4 text-stamp-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input id="staffSearch" type="text" placeholder="Search staff..." class="w-full bg-transparent outline-none text-sm text-stamp-700 placeholder-stamp-300 font-semibold">
        </div>
      </div>

      <div id="staffList" class="grid gap-3">
        @forelse ($staff as $member)
          <div class="staff-row bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap" data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}">
            <div class="w-12 h-12 rounded-full bg-stamp-100 flex items-center justify-center text-stamp-700 font-display font-bold text-lg shrink-0">{{ strtoupper(substr($member->first_name, 0, 1).substr($member->last_name, 0, 1)) }}</div>
            <div class="flex-1 min-w-[140px]">
              <p class="font-extrabold text-stamp-700">{{ $member->first_name }} {{ $member->middle_name ? substr($member->middle_name, 0, 1).'. ' : '' }}{{ $member->last_name }}</p>
              <p class="text-xs text-stamp-400 font-semibold">POS PIN &bull;&bull;&bull;&bull;</p>
            </div>
            <span class="bg-cream-100 text-stamp-600 text-xs font-extrabold px-3 py-1.5 rounded-full">{{ ucfirst($member->role) }}</span>
            <div class="flex items-center gap-2">
              <button type="button" onclick='openEditModal(@json($member))' class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
              <form method="POST" action="{{ route('admin.users.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->first_name }} {{ $member->last_name }}? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
              </form>
            </div>
          </div>
        @empty
          <p class="text-center text-sm font-semibold text-stamp-300 py-10">No staff accounts yet. Create one to get started.</p>
        @endforelse
      </div>
    </div>
  </main>

  <!-- Create Staff Modal -->
  <div id="createModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-lg p-7 relative max-h-[90vh] overflow-y-auto">

      <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Create Staff Account</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">Fill in the details to onboard a new team member</p>
      </div>

      <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">First Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Last Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Middle Name <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Santos" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Role</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="role" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="assistant" @selected(old('role') === 'assistant')>Assistant</option>
              <option value="admin" @selected(old('role') === 'admin')>Admin</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">POS PIN (4-digit)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset flex items-center gap-2 px-3.5 py-2.5">
            <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" name="passcode" value="{{ old('passcode') }}" placeholder="1234" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">This is the only credential this account uses &mdash; it unlocks the POS terminal.</p>
        </div>

        <div class="flex gap-3 pt-3">
          <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 py-3.5 rounded-2xl bg-cream-100 text-stamp-600 font-display font-bold text-sm hover:bg-cream-200 transition-colors">Cancel</button>
          <button type="submit" class="flex-1 py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Create Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Staff Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-lg p-7 relative max-h-[90vh] overflow-y-auto">

      <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Edit Staff Account</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">Update this team member's details</p>
      </div>

      <form id="editForm" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="_editing_id" id="edit_editing_id" value="{{ old('_editing_id') }}">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">First Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="first_name" id="edit_first_name" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Last Name</label>
            <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="last_name" id="edit_last_name" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Middle Name <span class="normal-case font-semibold text-stamp-300">(optional)</span></label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="middle_name" id="edit_middle_name" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Role</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5">
            <select name="role" id="edit_role" class="w-full bg-transparent outline-none text-stamp-700 font-semibold text-sm">
              <option value="assistant">Assistant</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">POS PIN (4-digit)</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset flex items-center gap-2 px-3.5 py-2.5">
            <svg class="w-4 h-4 text-stamp-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" name="passcode" id="edit_passcode" required class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm">
          </div>
        </div>

        <div class="flex gap-3 pt-3">
          <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 py-3.5 rounded-2xl bg-cream-100 text-stamp-600 font-display font-bold text-sm hover:bg-cream-200 transition-colors">Cancel</button>
          <button type="submit" class="flex-1 py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openSidebar(){
      document.getElementById('sidebar').classList.remove('-translate-x-full');
      document.getElementById('sidebarBackdrop').classList.remove('hidden');
    }
    function closeSidebar(){
      document.getElementById('sidebar').classList.add('-translate-x-full');
      document.getElementById('sidebarBackdrop').classList.add('hidden');
    }

    function openEditModal(credential){
      document.getElementById('editForm').action = "{{ url('/admin/users') }}/" + credential.id;
      document.getElementById('edit_editing_id').value = credential.id;
      document.getElementById('edit_first_name').value = credential.first_name || '';
      document.getElementById('edit_middle_name').value = credential.middle_name || '';
      document.getElementById('edit_last_name').value = credential.last_name || '';
      document.getElementById('edit_role').value = credential.role || 'assistant';
      document.getElementById('edit_passcode').value = credential.passcode || '';
      document.getElementById('editModal').classList.remove('hidden');
    }

    document.getElementById('staffSearch').addEventListener('input', function (e) {
      const query = e.target.value.trim().toLowerCase();
      document.querySelectorAll('.staff-row').forEach(function (row) {
        row.style.display = row.dataset.name.includes(query) ? '' : 'none';
      });
    });

    @if ($errors->any() && old('_editing_id'))
      openEditModal({
        id: {{ (int) old('_editing_id') }},
        first_name: @json(old('first_name')),
        middle_name: @json(old('middle_name')),
        last_name: @json(old('last_name')),
        role: @json(old('role')),
        passcode: @json(old('passcode')),
      });
    @elseif ($errors->any())
      document.getElementById('createModal').classList.remove('hidden');
    @endif
  </script>

</body>
</html>

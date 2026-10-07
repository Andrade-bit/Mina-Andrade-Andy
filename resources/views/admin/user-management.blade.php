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

  @include('admin.partials.sidebar', ['active' => 'users'])

  <!-- Main content -->
  <main class="flex-1 p-5 pt-20 md:p-8 overflow-y-auto">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
      <div>
        <h2 class="font-display font-bold text-2xl md:text-3xl text-stamp-700">User Management</h2>
        <p class="text-stamp-500 text-sm font-semibold mt-1">Create and manage staff accounts for Catbrews</p>
      </div>
      <button onclick="openModal('createModal')" class="bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold px-6 py-3.5 rounded-2xl shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create Staff Account
      </button>
    </div>

    <!-- Admin login -->
    <div class="bg-white rounded-[2rem] shadow-soft p-5 md:p-6 mb-6">
      <h3 class="font-display font-bold text-stamp-700 text-lg mb-4">Admin Login</h3>
      <div class="bg-cream-50 rounded-2xl shadow-soft-sm p-4 flex items-center gap-4 flex-wrap">
        <div class="w-12 h-12 rounded-full bg-stamp-700 flex items-center justify-center text-cream-50 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="flex-1 min-w-[140px]">
          <p class="font-extrabold text-stamp-700">{{ $admin->name }}</p>
          <p class="text-xs text-stamp-400 font-semibold">Password &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</p>
        </div>
        <span class="bg-cream-100 text-stamp-600 text-xs font-extrabold px-3 py-1.5 rounded-full">Dashboard login</span>
        <button type="button" onclick="openAccountModal()" title="Edit admin login" aria-label="Edit admin login" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
      </div>
    </div>

    <div class="flex justify-end mb-3">
      @if (request()->boolean('archived'))
        <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">&larr; Back to active</a>
      @else
        <a href="{{ route('admin.users', ['archived' => 1]) }}" class="px-4 py-2 rounded-2xl bg-cream-100 hover:bg-cream-200 text-stamp-600 font-extrabold text-xs transition-colors">View archived</a>
      @endif
    </div>


    <!-- Stat cards -->
    @php $archivedParam = request()->boolean('archived') ? ['archived' => 1] : []; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      <a href="{{ route('admin.users', $archivedParam) }}" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300 {{ $role === null ? 'ring-2 ring-stamp-500' : '' }}">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Total Staff</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $counts->sum() }}</p></div>
      </a>
      <a href="{{ route('admin.users', [...$archivedParam, 'role' => 'admin']) }}" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300 {{ $role === 'admin' ? 'ring-2 ring-stamp-500' : '' }}">
        <div class="w-12 h-12 rounded-2xl bg-cream-100 flex items-center justify-center text-stamp-500 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Admins</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $counts['admin'] ?? 0 }}</p></div>
      </a>
      <a href="{{ route('admin.users', [...$archivedParam, 'role' => 'assistant']) }}" class="bg-white rounded-3xl shadow-soft p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-transform outline-none focus-visible:ring-2 focus-visible:ring-stamp-300 {{ $role === 'assistant' ? 'ring-2 ring-stamp-500' : '' }}">
        <div class="w-12 h-12 rounded-2xl bg-mint-50 flex items-center justify-center text-mint-600 shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div><p class="text-[11px] font-extrabold uppercase tracking-wide text-stamp-300">Assistants</p><p class="font-display font-bold text-2xl text-stamp-700">{{ $counts['assistant'] ?? 0 }}</p></div>
      </a>
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
              @unless ($member->trashed())
              <button type="button" onclick='openEditModal(@json($member))' class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-stamp-100 flex items-center justify-center text-stamp-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
              @endunless
              @if ($member->trashed())
                <form method="POST" action="{{ route('admin.users.restore', $member->id) }}">
                  @csrf
                  <button type="submit" class="px-3.5 py-2 rounded-xl bg-mint-50 hover:bg-mint-500 hover:text-cream-50 text-mint-600 font-extrabold text-xs transition-colors">Restore</button>
                </form>
              @else
              <form method="POST" action="{{ route('admin.users.destroy', $member) }}" onsubmit="return confirm('Archive {{ $member->first_name }} {{ $member->last_name }}? {{ $member->sales_transactions_count ? 'Processed '.$member->sales_transactions_count.' sale(s). ' : '' }}You can restore it anytime.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Archive" class="w-9 h-9 rounded-xl bg-cream-100 hover:bg-coral-50 flex items-center justify-center text-coral-500 transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></button>
              </form>
              @endif
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
          @include('admin.partials.password-field', ['name' => 'passcode', 'pin' => true, 'placeholder' => '1234', 'required' => true, 'value' => old('passcode')])
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
          @include('admin.partials.password-field', ['name' => 'passcode', 'id' => 'edit_passcode', 'pin' => true, 'required' => true])
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

  <!-- Edit Admin Login Modal -->
  <div id="accountModal" class="hidden fixed inset-0 bg-stamp-700/30 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-cream-50 rounded-[2rem] shadow-soft w-full max-w-lg p-7 relative max-h-[90vh] overflow-y-auto">

      <button type="button" onclick="document.getElementById('accountModal').classList.add('hidden')" class="absolute top-6 right-6 text-stamp-300 hover:text-stamp-600">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-cream-100 shadow-soft-inset mx-auto flex items-center justify-center text-stamp-600 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h3 class="font-display font-bold text-xl text-stamp-700">Edit Admin Login</h3>
        <p class="text-xs text-stamp-400 font-semibold mt-1">The username and password used to sign in to this dashboard</p>
      </div>

      <form id="accountForm" method="POST" action="{{ route('admin.account.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Username</label>
          <div class="bg-cream-100 rounded-2xl shadow-soft-inset px-3.5 py-2.5"><input type="text" name="username" value="{{ $admin->name }}" required autocomplete="username" class="w-full bg-transparent outline-none text-stamp-700 placeholder-stamp-300 font-semibold text-sm"></div>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">New Password <span class="normal-case font-semibold text-stamp-300">(leave blank to keep the current one)</span></label>
          @include('admin.partials.password-field', ['name' => 'password', 'placeholder' => 'At least 8 characters', 'autocomplete' => 'new-password'])
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-stamp-500 mb-1.5 ml-1">Current Password</label>
          @include('admin.partials.password-field', ['name' => 'current_password', 'placeholder' => 'Needed to save any change', 'required' => true, 'autocomplete' => 'current-password'])
          <p class="text-[11px] text-stamp-300 font-semibold mt-1.5 ml-1">Your current password is stored scrambled, so it can't be shown. Type it to confirm it's you.</p>
        </div>

        <div class="flex gap-3 pt-3">
          <button type="button" onclick="document.getElementById('accountModal').classList.add('hidden')" class="flex-1 py-3.5 rounded-2xl bg-cream-100 text-stamp-600 font-display font-bold text-sm hover:bg-cream-200 transition-colors">Cancel</button>
          <button type="submit" class="flex-1 py-3.5 rounded-2xl bg-gradient-to-b from-stamp-500 to-stamp-600 text-cream-50 font-display font-bold text-sm shadow-soft-btn active:shadow-none active:translate-y-[5px] transition-all duration-150 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function setPasswordVisible(box, visible){
      const input = box.querySelector('input');
      const eye = box.querySelector('[data-eye]');
      input.type = visible ? 'text' : 'password';
      eye.setAttribute('aria-pressed', visible ? 'true' : 'false');
      eye.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
      eye.querySelector('.eye-show').classList.toggle('hidden', visible);
      eye.querySelector('.eye-hide').classList.toggle('hidden', !visible);
    }

    // Every modal opens with its passwords and PINs hidden again.
    function openModal(id){
      const modal = document.getElementById(id);
      modal.querySelectorAll('[data-eye]').forEach(function (eye) { setPasswordVisible(eye.parentElement, false); });
      modal.classList.remove('hidden');
    }

    // The eye button keeps the field focused (mousedown) and flips between dots and the typed text (click).
    document.addEventListener('mousedown', function (e) {
      if (e.target.closest('[data-eye]')) { e.preventDefault(); }
    });
    document.addEventListener('click', function (e) {
      const eye = e.target.closest('[data-eye]');
      if (eye) { setPasswordVisible(eye.parentElement, eye.parentElement.querySelector('input').type === 'password'); }
    });

    function openAccountModal(){
      const form = document.getElementById('accountForm');
      form.reset();
      form.querySelectorAll('.cbt-invalid').forEach(function (el) { el.classList.remove('cbt-invalid'); });
      openModal('accountModal');
    }

    function openEditModal(credential){
      document.getElementById('editForm').action = "{{ route('admin.users.update', ':id') }}".replace(':id', credential.id);
      document.getElementById('edit_editing_id').value = credential.id;
      document.getElementById('edit_first_name').value = credential.first_name || '';
      document.getElementById('edit_middle_name').value = credential.middle_name || '';
      document.getElementById('edit_last_name').value = credential.last_name || '';
      document.getElementById('edit_role').value = credential.role || 'assistant';
      document.getElementById('edit_passcode').value = credential.passcode || '';
      openModal('editModal');
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

  @include('admin.partials.toasts')

</body>
</html>

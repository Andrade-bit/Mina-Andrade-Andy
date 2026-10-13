<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Shop activity</title><script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{stamp:{300:'#8FBBdd',500:'#2F6690',600:'#24506F',700:'#1A3B52'},cream:{50:'#FFFDF9',100:'#FBF3E4'},coral:{500:'#E0776B'}}}}}</script></head>
<body class="bg-cream-50 text-stamp-700">
@include('admin.partials.sidebar', ['active' => 'shop-activity'])
<main class="p-6 pt-24 md:p-10">
<h1 class="text-3xl font-bold mb-3">Shop activity</h1><p class="mb-6 text-stamp-500">See what changed in your shop and who made the change.</p>
<form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
<label class="grid gap-2 text-sm font-semibold">Activity type<select name="subject" class="border rounded-xl p-3 bg-white"><option value="">All activities</option>@foreach($recordTypes as $key => $label)<option value="{{ $key }}" @selected(request('subject') === $key)>{{ $label }}</option>@endforeach</select></label>
<label class="grid gap-2 text-sm font-semibold">From<input type="date" name="from" value="{{ request('from') }}" class="border rounded-xl p-3"></label><label class="grid gap-2 text-sm font-semibold">To<input type="date" name="to" value="{{ request('to') }}" class="border rounded-xl p-3"></label><button class="rounded-xl bg-stamp-500 text-white px-5 py-3 font-semibold">Filter</button><a href="{{ route('admin.shop-activity') }}" class="px-3 py-3 text-sm">Clear filters</a>
</form>
@foreach($errors->all() as $error)<p class="text-red-700 mb-3">{{ $error }}</p>@endforeach
@forelse($activities as $activity)
@php
$type = $recordTypes[$activity->subject_type] ?? \Illuminate\Support\Str::headline($activity->subject_type);
$action = ['created' => 'Added', 'updated' => 'Updated', 'archived' => 'Archived', 'restored' => 'Restored', 'deleted' => 'Removed', 'password_reset' => 'Password reset'][$activity->action] ?? \Illuminate\Support\Str::headline($activity->action);
$rows = \App\Support\ActivityDisplay::rows($activity, $inventoryNames);
$comparison = ! empty($activity->changes['before']);
$name = $activity->subject_type === 'InventoryItem' ? ($inventoryNames[$activity->subject_id] ?? null) : null;
$actor = $activity->credential_id ? ($staffNames[$activity->credential_id] ?? 'POS staff') : ($activity->user_id ? ($adminNames[$activity->user_id] ?? 'Shop admin') : 'System');
@endphp
<article class="bg-white border border-stamp-300/30 rounded-2xl p-5 mb-4">
<div class="flex flex-wrap items-center justify-between gap-2"><h2 class="font-bold text-lg">{{ $type }}{{ $name ? ' · '.$name : '' }}</h2><span class="text-xs font-bold px-3 py-1 rounded-full bg-cream-100">{{ $action }}</span></div>
<p class="text-sm text-stamp-500 mt-1 mb-3">{{ $activity->created_at->format('M j, Y · g:i A') }} UTC · {{ $actor }}</p>
@if($rows)<details><summary class="cursor-pointer text-sm font-semibold text-stamp-500">{{ $comparison ? 'See what changed' : 'View details' }}</summary><div class="overflow-x-auto mt-4"><table class="w-full text-sm text-left"><thead><tr class="bg-cream-50"><th class="p-3 font-semibold">Detail</th>@if($comparison)<th class="p-3 font-semibold">Previous</th>@endif<th class="p-3 font-semibold">{{ $comparison ? 'New' : 'Value' }}</th></tr></thead><tbody>
@foreach($rows as $row)<tr class="border-t border-stamp-300/20"><th class="p-3 font-semibold">{{ $row['label'] }}</th>@if($comparison)<td class="p-3 text-stamp-500">{{ $row['before'] }}</td>@endif<td class="p-3">{{ $row['after'] }}</td></tr>@endforeach
</tbody></table></div></details>@else<p class="text-sm text-stamp-500">{{ $action }} in your shop.</p>@endif
</article>
@empty<p class="bg-white rounded-2xl border p-6">No activities match these filters.</p>@endforelse
{{ $activities->links() }}
</main></body></html>

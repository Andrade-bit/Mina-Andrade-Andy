@extends('superadmin.layout')
@section('title', 'System Activity')
@section('content')
<header class="heading"><div><h1>System Activity</h1><p class="subtitle">An audit trail of platform account and subscription changes.</p></div></header><section class="card table-wrap"><table><thead><tr><th>Time (Manila)</th><th>Administrator</th><th>Shop</th><th>Change</th></tr></thead><tbody>@forelse($activities as $activity)<tr><td>{{ $activity->created_at->timezone('Asia/Manila')->format('M j, Y g:i a') }}</td><td>{{ $activity->user?->name ?? 'System' }}</td><td>{{ $activity->shop?->name ?? 'Platform' }}</td><td>{{ $activity->description }}</td></tr>@empty<tr><td colspan="4" class="muted">No platform changes recorded yet.</td></tr>@endforelse</tbody></table>@include('superadmin.partials.pagination', ['paginator' => $activities])</section>
@endsection

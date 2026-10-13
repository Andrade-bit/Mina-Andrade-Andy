@extends('superadmin.layout')
@section('title', 'Platform overview')
@section('content')
<header class="heading"><div><h1>Platform overview</h1><p class="subtitle">Manage shop accounts and subscriptions from one place.</p></div><a class="btn" href="{{ route('superadmin.shops.create') }}">＋ Add shop</a></header>
<div class="stats">
  @foreach(['Total shops' => $totalShops, 'Active subscriptions' => $activeSubscriptions, 'Monthly recurring value' => '₱'.number_format($monthlyRevenue, 2), 'Needs attention' => $attention->count()] as $label => $value)
  <div class="card"><div class="stat-label">{{ $label }}</div><p class="stat-number">{{ $value }}</p></div>
  @endforeach
</div>
<div class="split"><section class="card"><div class="heading"><h2>Shops</h2><a href="{{ route('superadmin.shops.index') }}">View all →</a></div>@include('superadmin.partials.shop-table')</section>
<div><section class="card"><h2>Needs attention</h2>@forelse($attention as $shop)<div class="activity-row"><strong>{{ $shop->name }}</strong><p class="muted tiny">{{ str($shop->subscription_status)->replace('_', ' ')->title() }}{{ $shop->trial_ends_at && $shop->subscription_status === 'trial' ? ' · Trial ends '.$shop->trial_ends_at->timezone('Asia/Manila')->format('M j, Y') : '' }}</p><a href="{{ route('superadmin.shops.edit', $shop) }}">Review subscription →</a></div>@empty<p class="muted">All subscriptions are up to date.</p>@endforelse</section>
<section class="card"><h2>Recent activity</h2>@forelse($activities as $activity)<div class="activity-row"><strong>{{ $activity->description }}</strong><br><span class="muted tiny">{{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->timezone('Asia/Manila')->format('M j, g:i a') }}</span></div>@empty<p class="muted">Platform changes will appear here.</p>@endforelse</section></div></div>
<p class="muted tiny">Recurring value reflects configured plans, not collected payments. Shop inventory, sales, and reports remain private to each shop.</p>
@endsection

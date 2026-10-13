@extends('superadmin.layout')
@section('title', 'Subscriptions')
@section('content')
<header class="heading"><div><h1>Subscriptions</h1><p class="subtitle">Manage trials and renewals. Payments are recorded outside this platform.</p></div></header>
<section class="card table-wrap"><table><thead><tr><th>Shop</th><th>Plan</th><th>Monthly price</th><th>Status</th><th>Trial ends</th><th>Next renewal</th><th>Action</th></tr></thead><tbody>@foreach($shops as $shop)<tr><td>{{ $shop->name }}</td><td>{{ $shop->plan->name }}</td><td>₱{{ number_format($shop->plan->monthly_price, 2) }}</td><td><span class="badge {{ $shop->subscription_status }}">{{ str($shop->subscription_status)->replace('_', ' ')->title() }}</span></td><td>{{ $shop->trial_ends_at?->timezone('Asia/Manila')->format('M j, Y') ?? '—' }}</td><td>{{ $shop->renews_at?->timezone('Asia/Manila')->format('M j, Y') ?? 'No expiry' }}</td><td><a href="{{ route('superadmin.shops.edit', $shop) }}">Manage →</a></td></tr>@endforeach</tbody></table>@include('superadmin.partials.pagination', ['paginator' => $shops])</section>
@endsection

@extends('superadmin.layout')
@section('title', 'Email delivery')
@section('content')
<header class="heading"><div><h1>Email delivery</h1><p class="subtitle">{{ $enabled ? 'Email sending enabled.' : 'Email sending disabled until configured.' }} Recovery and invitation links are never displayed here.</p></div></header>
<section class="card"><div class="table-wrap"><table><thead><tr><th>Requested</th><th>Account / Shop</th><th>Purpose</th><th>Status</th><th>Attempts</th></tr></thead><tbody>@forelse($messages as $message)<tr><td>{{ $message->created_at }}</td><td>#{{ $message->user_id }} / #{{ $message->shop_id }}</td><td>{{ ucfirst($message->kind) }}</td><td>{{ ucfirst($message->status) }}</td><td>{{ $message->attempts }}</td></tr>@empty<tr><td colspan="5">No email requests yet.</td></tr>@endforelse</tbody></table></div>@include('superadmin.partials.pagination', ['paginator' => $messages])</section>
@endsection

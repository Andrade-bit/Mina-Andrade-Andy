@extends('superadmin.layout')
@section('title', 'Shops')
@section('content')
<header class="heading"><div><h1>Shops</h1><p class="subtitle">Manage shop access without opening private shop records.</p></div><a class="btn" href="{{ route('superadmin.shops.create') }}">＋ Add shop</a></header>
<section class="card"><form class="toolbar" method="GET"><input aria-label="Search shops" name="search" maxlength="120" value="{{ request('search') }}" placeholder="Search shops…"><select name="status" aria-label="Shop status"><option value="">All statuses</option>@foreach(['active', 'suspended'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="btn">Search</button><a href="{{ route('superadmin.shops.index') }}">Clear</a></form>@include('superadmin.partials.shop-table')@include('superadmin.partials.pagination', ['paginator' => $shops])</section>
@endsection

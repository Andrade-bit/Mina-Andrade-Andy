@extends('superadmin.layout')
@section('title', 'Plans')
@section('content')
<header class="heading"><div><h1>Plans</h1><p class="subtitle">Set monthly pricing and product and POS staff limits.</p></div></header>
<div class="grid">@foreach($plans as $plan)<section class="card"><h2>{{ $plan->name }}</h2><p class="muted">{{ $plan->shops_count }} shops · ₱{{ number_format($plan->monthly_price, 2) }}/month</p><details><summary>Edit plan</summary>@include('superadmin.partials.plan-form', ['plan' => $plan])</details></section>@endforeach</div>
<section class="card"><h2>Create plan</h2>@include('superadmin.partials.plan-form', ['plan' => new \App\Models\Plan])</section>
@endsection

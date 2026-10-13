@extends('superadmin.layout')
@section('title', $shop->exists ? 'Manage shop' : 'Add shop')
@section('content')
<header class="heading"><div><h1>{{ $shop->exists ? 'Manage '.$shop->name : 'Add shop' }}</h1><p class="subtitle">{{ $shop->exists ? 'Manage access and subscription details.' : 'Create a separate workspace and its owner login.' }}</p></div><a class="btn secondary" href="{{ route('superadmin.shops.index') }}">← Shops</a></header>
<form class="card stack" method="POST" action="{{ $shop->exists ? route('superadmin.shops.update', $shop) : route('superadmin.shops.store') }}">@csrf @if($shop->exists)@method('PUT')@endif
  <h2>Shop details</h2><div class="grid">
    <div class="field"><label for="shop-name">Shop name</label><input id="shop-name" name="name" value="{{ old('name', $shop->name) }}" required maxlength="100"></div>
    <div class="field"><label for="shop-slug">Shop code</label><input id="shop-slug" name="slug" value="{{ old('slug', $shop->slug) }}" required maxlength="80" pattern="[a-z0-9]+(-[a-z0-9]+)*"><p class="muted tiny">Lowercase letters, numbers, and hyphens. Used for this shop’s menu and POS links.</p></div>
    <div class="field"><label for="plan">Plan</label><select id="plan" name="plan_id" required>@foreach($plans as $plan)<option value="{{ $plan->id }}" @selected((int) old('plan_id', $shop->plan_id) === $plan->id)>{{ $plan->name }} · ₱{{ number_format($plan->monthly_price, 2) }}/month</option>@endforeach</select></div>
    @if($shop->exists)
    <div class="field"><label for="status">Shop access</label><select id="status" name="status">@foreach(['active', 'suspended'] as $status)<option value="{{ $status }}" @selected(old('status', $shop->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
    <div class="field"><label for="subscription">Subscription</label><select id="subscription" name="subscription_status">@foreach(['trial', 'active', 'past_due', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('subscription_status', $shop->subscription_status) === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
    <div class="field"><label for="trial">Trial ends</label><input id="trial" type="date" name="trial_ends_at" value="{{ old('trial_ends_at', $shop->trial_ends_at?->format('Y-m-d')) }}"></div>
    <div class="field"><label for="renewal">Next renewal</label><input id="renewal" type="date" name="renews_at" value="{{ old('renews_at', $shop->renews_at?->format('Y-m-d')) }}"><p class="muted tiny">Leave blank for access without an expiry date.</p></div>
    @else
    <div class="field"><label for="owner">Owner username</label><input id="owner" name="owner_name" value="{{ old('owner_name') }}" autocomplete="off" required maxlength="255"></div>
    <div class="field"><label for="email">Owner email</label><input id="email" type="email" name="owner_email" value="{{ old('owner_email') }}" required maxlength="255"></div>
    <p class="muted">The owner will receive an expiring email invitation and choose their own password.</p>
    @endif
  </div>
  <p class="muted">{{ $shop->exists ? 'Suspended shops, expired subscriptions, and expired trials cannot access shop operations. No records are deleted.' : 'New shops start with a '.$trialDays.'-day trial and an empty inventory, menu, and sales history.' }}</p>
  <div><button class="btn">{{ $shop->exists ? 'Save shop settings' : 'Create shop & owner' }}</button></div>
</form>
@if($shop->exists)<section class="card"><h2>Shop links</h2><p><a href="{{ route('home', ['shop' => $shop->slug]) }}">Public menu</a> · <a href="{{ route('pos.login', ['shop' => $shop->slug]) }}">Shop POS login</a> · <a href="{{ route('admin.login') }}">Shared admin login</a></p><p class="muted tiny">Owners and managers use the same admin login page. These links do not grant access to private shop records.</p></section>@endif
@endsection

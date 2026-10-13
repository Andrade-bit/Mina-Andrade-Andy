@extends('superadmin.layout')
@section('title', 'Settings')
@section('content')
<header class="heading"><div><h1>Platform settings</h1><p class="subtitle">Defaults for the superadmin area and new shops.</p></div></header>
<form class="card stack" method="POST" action="{{ route('superadmin.settings.update') }}">@csrf @method('PUT')<div class="grid"><div class="field"><label>Platform name<input name="platform_name" value="{{ old('platform_name', $settings->platform_name) }}" required maxlength="80"></label></div><div class="field"><label>Support email<input type="email" name="support_email" value="{{ old('support_email', $settings->support_email) }}"></label></div><div class="field"><label>New shop trial (days)<input type="number" name="trial_days" min="1" max="90" value="{{ old('trial_days', $settings->trial_days) }}" required></label></div></div><div><button class="btn">Save settings</button></div></form>
@endsection

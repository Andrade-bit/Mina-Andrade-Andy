<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlatformActivity;
use App\Models\PlatformSetting;
use App\Models\Shop;
use App\Models\User;
use App\Support\AccountInvitations;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', Rule::in(['active', 'suspended'])]]);

        return view('superadmin.shops', ['shops' => Shop::with('plan', 'users')
            ->when($data['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('name')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return view('superadmin.shop-form', ['shop' => new Shop, 'plans' => Plan::where('is_active', true)->get(), 'trialDays' => PlatformSetting::findOrFail(1)->trial_days]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:80', 'unique:shops,slug'],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('is_active', true)],
            'owner_name' => ['required', 'string', 'max:255', 'unique:users,name'], 'owner_email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);
        $shop = DB::transaction(function () use ($data, $request): Shop {
            $shop = Shop::create(['name' => $data['name'], 'slug' => $data['slug'], 'plan_id' => $data['plan_id'], 'status' => 'active', 'subscription_status' => 'trial', 'trial_ends_at' => now()->addDays(PlatformSetting::findOrFail(1)->trial_days)]);
            $owner = User::create(['name' => $data['owner_name'], 'email' => $data['owner_email'], 'password' => Str::random(64), 'shop_id' => $shop->id, 'role' => 'owner', 'is_active' => true]);
            app(AccountInvitations::class)->send($owner);
            PlatformActivity::create(['user_id' => $request->user()->id, 'shop_id' => $shop->id, 'action' => 'shop.created', 'description' => 'Shop created: '.$shop->name]);

            return $shop;
        });

        return redirect()->route('superadmin.shops.edit', $shop)->with('status', 'Shop created. An invitation has been queued for the owner to choose their own password.');
    }

    public function edit(Shop $shop): View
    {
        return view('superadmin.shop-form', ['shop' => $shop->load('users', 'plan'), 'plans' => Plan::where('is_active', true)->orWhere('id', $shop->plan_id)->get(), 'trialDays' => PlatformSetting::findOrFail(1)->trial_days]);
    }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:80', Rule::unique('shops', 'slug')->ignore($shop->id)],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $shop->plan_id))],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'subscription_status' => ['required', Rule::in(['trial', 'active', 'past_due', 'cancelled'])],
            'trial_ends_at' => ['required_if:subscription_status,trial', 'nullable', 'date'],
            'renews_at' => ['nullable', 'date'],
        ]);
        DB::transaction(function () use ($shop, $data, $request): void {
            $shop->update($data);
            PlatformActivity::create(['user_id' => $request->user()->id, 'shop_id' => $shop->id, 'action' => 'shop.updated', 'description' => 'Shop settings updated: '.$shop->name.'; status '.$shop->status.'; subscription '.$shop->subscription_status]);
        });

        return back()->with('status', 'Shop settings updated.');
    }

    public function subscriptions(): View
    {
        return view('superadmin.subscriptions', ['shops' => Shop::with('plan')->orderBy('name')->paginate(20)]);
    }
}

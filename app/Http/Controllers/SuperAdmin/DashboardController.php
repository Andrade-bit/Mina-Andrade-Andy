<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformActivity;
use App\Models\PlatformSetting;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $active = Shop::where('status', 'active')->where('subscription_status', 'active')
            ->where(fn ($query) => $query->whereNull('renews_at')->orWhere('renews_at', '>', now()));

        return view('superadmin.dashboard', [
            'totalShops' => Shop::count(),
            'activeSubscriptions' => (clone $active)->count(),
            'monthlyRevenue' => (clone $active)->with('plan')->get()->sum(fn (Shop $shop) => (float) $shop->plan->monthly_price),
            'attention' => Shop::with('plan')->where(function ($query): void {
                $query->whereIn('subscription_status', ['past_due', 'cancelled'])
                    ->orWhere(fn ($trial) => $trial->where('subscription_status', 'trial')->where('trial_ends_at', '<=', now()->addDays(7)))
                    ->orWhere(fn ($active) => $active->where('subscription_status', 'active')->whereNotNull('renews_at')->where('renews_at', '<=', now()));
            })->get(),
            'shops' => Shop::with('plan', 'users')->latest()->limit(6)->get(),
            'activities' => PlatformActivity::with('user', 'shop')->latest()->limit(5)->get(),
        ]);
    }

    public function activity(): View
    {
        return view('superadmin.activity', ['activities' => PlatformActivity::with('user', 'shop')->latest()->paginate(25)]);
    }

    public function settings(): View
    {
        return view('superadmin.settings', ['settings' => PlatformSetting::findOrFail(1)]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate(['platform_name' => ['required', 'string', 'max:80'], 'support_email' => ['nullable', 'email', 'max:255'], 'trial_days' => ['required', 'integer', 'min:1', 'max:90']]);
        DB::transaction(function () use ($data, $request): void {
            PlatformSetting::findOrFail(1)->update($data);
            PlatformActivity::create(['user_id' => $request->user()->id, 'action' => 'settings.updated', 'description' => 'Platform settings updated.']);
        });

        return back()->with('status', 'Platform settings updated.');
    }
}

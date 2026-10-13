<?php

namespace App\Support;

use App\Models\EmailMessage;
use App\Models\Shop;
use App\Models\User;

class SubscriptionReminders
{
    public function collect(): void
    {
        Shop::with('plan')->where('status', 'active')->whereIn('subscription_status', ['trial', 'active'])->chunkById(100, function ($shops): void {
            foreach ($shops as $shop) {
                if (! $shop->plan?->allows('notifications')) {
                    continue;
                }
                $expiry = $shop->subscription_status === 'trial' ? $shop->trial_ends_at : $shop->renews_at;
                if (! $expiry) {
                    continue;
                }
                $days = (int) now()->startOfDay()->diffInDays($expiry->copy()->startOfDay(), false);
                if ($days > 7) {
                    continue;
                }
                $stage = $days < 0 ? 'expired' : ($days === 0 ? 'today' : 'upcoming');
                $label = $shop->subscription_status === 'trial' ? 'trial' : 'subscription';
                User::where('shop_id', $shop->id)->where('role', 'owner')->where('is_active', true)->whereNotNull('email_verified_at')->whereNull('invitation_token')->each(function (User $owner) use ($shop, $expiry, $stage, $label): void {
                    app(ServiceMail::class)->queue($owner, 'subscription', ['subject' => $shop->name.' '.$label.' reminder', 'lines' => ['Your '.$shop->name.' '.$label.' '.($stage === 'expired' ? 'expired on ' : 'ends on ').$expiry->format('F j, Y').'.', 'Contact platform support to arrange renewal.'], 'action' => 'Sign in', 'url' => ServiceMail::url('admin/login')], 'subscription:'.$shop->id.':'.$owner->id.':'.$label.':'.$expiry->format('Y-m-d').':'.$stage, now()->addDays(2));
                });
            }
        });
    }

    public function process(int $limit = 20): int
    {
        $sent = 0;
        $started = microtime(true);
        EmailMessage::whereIn('status', ['pending', 'failed'])->where('available_at', '<=', now())->orderBy('id')->limit($limit)->pluck('id')->each(function ($id) use (&$sent, $started) {
            if (! app()->runningInConsole() && microtime(true) - $started > 18) {
                return false;
            }
            $sent += (int) app(ServiceMail::class)->deliver($id);
        });

        return $sent;
    }
}

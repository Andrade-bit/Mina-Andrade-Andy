<?php

namespace App\Observers;

use App\Models\ShopActivity;
use App\Models\User;
use App\Support\ServiceMail;
use App\Support\ShopContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountChangeObserver
{
    public function updated(User $user): void
    {
        $passwordChanged = $user->wasChanged('password');
        $accessChanged = $user->wasChanged(['role', 'is_active']);
        $activationChanged = $user->wasChanged('is_active');
        if ((int) $user->shop_id === app(ShopContext::class)->id() && ! $user->isSuperAdmin() && $user->wasChanged(['password', 'name', 'role', 'is_active'])) {
            ShopActivity::create(['user_id' => auth()->id(), 'action' => 'updated', 'subject_type' => 'User', 'subject_id' => $user->id, 'changes' => ['password_changed' => $user->wasChanged('password'), 'username_changed' => $user->wasChanged('name'), 'role' => $user->role, 'is_active' => $user->is_active]]);
        }
        if ($passwordChanged) {
            $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();
        }
        if (! $user->email_verified_at || $user->invitation_token || ! ($passwordChanged || $accessChanged)) {
            return;
        }
        $mail = app(ServiceMail::class);
        $message = $mail->queue($user, 'account_change', ['subject' => 'Your Catbrews account was updated', 'lines' => ['Your password or account access was changed.', 'If you did not expect this change, contact platform support immediately.']], 'account:'.$user->id.':'.Str::uuid(), now()->addDays(2));
        DB::afterCommit(fn () => $mail->deliver($message->id));
        if ($passwordChanged || $activationChanged) {
            DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', request()->hasSession() ? request()->session()->getId() : '')->delete();
        }
    }
}

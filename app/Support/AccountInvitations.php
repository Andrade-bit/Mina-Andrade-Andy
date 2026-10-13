<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountInvitations
{
    public function send(User $user): void
    {
        $token = Str::random(64);
        $mail = app(ServiceMail::class);
        $user->forceFill(['invitation_token' => hash('sha256', $token), 'invitation_expires_at' => now()->addHours(48)])->save();
        $mail->cancel($user, 'invitation');
        $message = $mail->queue($user, 'invitation', ['subject' => 'Your Catbrews account invitation', 'lines' => ['You have been invited to '.$user->shop->name.'. Choose your own password using the link below.', 'This link expires in 48 hours.'], 'action' => 'Accept invitation', 'url' => ServiceMail::url('account/invitation/'.$token)], 'invite:'.$user->id.':'.$token, $user->invitation_expires_at);
        DB::afterCommit(fn () => $mail->deliver($message->id));
    }
}

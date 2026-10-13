<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ServiceMail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class RecoveryController extends Controller
{
    public function form(Request $request, ?string $token = null): View
    {
        return view('auth.recovery', ['mode' => $token ? 'reset' : 'forgot', 'token' => $token, 'email' => $request->query('email')]);
    }

    public function forgot(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $user = User::where('email', $data['email'])->where('is_active', true)->whereNull('invitation_token')->first();
        if ($user) {
            Password::sendResetLink(['email' => $user->email], function (User $user, string $token): void {
                $mail = app(ServiceMail::class);
                $mail->cancel($user, 'recovery');
                $message = $mail->queue($user, 'recovery', ['subject' => 'Reset your Catbrews password', 'lines' => ['Use the link below to choose a new password.', 'This link expires in 60 minutes. If you did not request it, ignore this email.'], 'action' => 'Reset password', 'url' => ServiceMail::url('account/reset/'.$token.'?'.http_build_query(['email' => $user->email]))], 'reset:'.$user->id.':'.$token, now()->addMinutes(60));
                $mail->deliver($message->id);
            });
        }

        return back()->with('status', 'If an eligible account matches that email, a recovery link will be sent.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'token' => ['required', 'string'], 'password' => ['required', 'confirmed', PasswordRule::min(12)->mixedCase()->numbers()]]);
        $result = DB::transaction(function () use ($data) {
            DB::table(config('auth.passwords.users.table'))->where('email', $data['email'])->lockForUpdate()->first();

            return Password::reset([...$data, 'is_active' => true, 'invitation_token' => null], function (User $user, string $password): void {
                DB::transaction(function () use ($user, $password): void {
                    $user->forceFill(['password' => $password, 'remember_token' => Str::random(60), 'email_verified_at' => now()])->save();
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                    if ($user->shop_id && ! $user->isSuperAdmin()) {
                        DB::table('shop_activities')->insert(['shop_id' => $user->shop_id, 'user_id' => $user->id, 'action' => 'password_reset', 'subject_type' => 'User', 'subject_id' => (string) $user->id, 'changes' => json_encode(['password_changed' => true]), 'created_at' => now(), 'updated_at' => now()]);
                    }
                    app(ServiceMail::class)->cancel($user, 'recovery');
                    event(new PasswordReset($user));
                });
            });
        });
        if ($result !== Password::PasswordReset) {
            return back()->withErrors(['email' => 'The recovery link is invalid or expired. Please request another.']);
        }

        return redirect()->route('admin.login')->with('status', 'Password reset. Sign in with your new password.');
    }

    public function invitation(string $token): View
    {
        $this->invitedUser($token);

        return view('auth.recovery', ['mode' => 'invitation', 'token' => $token, 'email' => null]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'confirmed', PasswordRule::min(12)->mixedCase()->numbers()]]);
        DB::transaction(function () use ($data, $token): void {
            $user = $this->invitedUser($token, true);
            $user->forceFill(['password' => $data['password'], 'email_verified_at' => now(), 'invitation_token' => null, 'invitation_expires_at' => null, 'remember_token' => Str::random(60)])->save();
            app(ServiceMail::class)->cancel($user, 'invitation');
        });

        return redirect()->route('admin.login')->with('status', 'Invitation accepted. Sign in with your username or email.');
    }

    private function invitedUser(string $token, bool $lock = false): User
    {
        abort_unless(strlen($token) === 64, 404);

        return User::where('invitation_token', hash('sha256', $token))->where('is_active', true)->where('invitation_expires_at', '>', now())->when($lock, fn ($query) => $query->lockForUpdate())->firstOrFail();
    }
}

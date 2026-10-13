<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Auth\RecoveryController;
use App\Http\Controllers\Controller;
use App\Models\EmailMessage;
use App\Models\User;
use App\Support\AccountInvitations;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(): View
    {
        return view('superadmin.emails', ['messages' => EmailMessage::select(['id', 'user_id', 'shop_id', 'kind', 'status', 'attempts', 'created_at', 'sent_at'])->latest()->paginate(30), 'enabled' => config('saas.email_enabled')]);
    }

    public function invite(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin() || ! $user->is_active, 403);
        if ($user->invitation_token) {
            app(AccountInvitations::class)->send($user);
        } else {
            $recoveryRequest = Request::create('/account/forgot', 'POST', ['email' => $user->email]);
            $recoveryRequest->setLaravelSession($request->session());
            app(RecoveryController::class)->forgot($recoveryRequest);
        }

        return back()->with('status', 'Account email requested. Check Email delivery for its status.');
    }
}

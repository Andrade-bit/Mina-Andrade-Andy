<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Show the admin dashboard login form.
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * Authenticate the admin/owner using the standard users table.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            filter_var($validated['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name' => $validated['username'],
            'password' => $validated['password'],
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Those credentials do not match our records.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->forget(['pos_credential_id', 'pos_credential_role']);

        if (Auth::user()->isSuperAdmin()) {
            PlatformActivity::create(['user_id' => Auth::id(), 'action' => 'superadmin.login', 'description' => 'Platform administrator signed in.']);

            return redirect()->route('superadmin.dashboard');
        }
        if (! Auth::user()->shop?->canOperate()) {
            Auth::logout();

            return back()->withErrors(['username' => 'This shop is unavailable. Please contact platform support.'])->onlyInput('username');
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Log the admin out of the dashboard.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

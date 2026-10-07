<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DashboardLockController extends Controller
{
    /**
     * Ask for the admin password to get back into a dashboard that was locked when the POS was opened.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('dashboard_locked')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.unlock');
    }

    /**
     * Unlock the dashboard when the signed-in admin's password is right.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($validated['password'], $request->user()->getAuthPassword())) {
            throw ValidationException::withMessages(['password' => 'That password is not right.']);
        }

        $request->session()->forget('dashboard_locked');
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}

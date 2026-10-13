<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Support\ShopContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PosAuthController extends Controller
{
    /**
     * Show the POS passcode entry screen.
     */
    public function create(): View
    {
        return view('pos.login', ['shopSlug' => app(ShopContext::class)->shop()?->slug]);
    }

    /**
     * Unlock the POS terminal for whoever owns the passcode.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'passcode' => ['required', 'digits:4'],
        ]);

        $credential = Credential::where('passcode', $validated['passcode'])->first();

        if (! $credential) {
            return back()->withErrors(['passcode' => 'Incorrect PIN. Please try again.']);
        }

        $request->session()->regenerate();
        $request->session()->put('pos_credential_id', $credential->id);
        $request->session()->put('pos_credential_role', $credential->role);

        return redirect()->route('pos.terminal');
    }

    /**
     * Lock the POS terminal and return to the passcode screen.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $shopSlug = app(ShopContext::class)->shop()?->slug;
        $request->session()->forget(['pos_credential_id', 'pos_credential_role']);

        return redirect()->route('pos.login', ['shop' => $shopSlug]);
    }
}

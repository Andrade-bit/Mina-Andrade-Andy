<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List every staff/admin account.
     */
    public function index(): View
    {
        $staff = Credential::orderBy('first_name')->get();

        return view('admin.user-management', [
            'staff' => $staff,
        ]);
    }

    /**
     * Create a new staff or admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'assistant'])],
            'passcode' => ['required', 'digits:4', 'unique:credentials,passcode'],
        ]);

        Credential::create($validated);

        return redirect()->route('admin.users')->with('status', 'Staff account created.');
    }

    /**
     * Update an existing staff or admin account.
     */
    public function update(Request $request, Credential $credential): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'assistant'])],
            'passcode' => ['required', 'digits:4', Rule::unique('credentials', 'passcode')->ignore($credential->id)],
        ]);

        $credential->update($validated);

        return redirect()->route('admin.users')->with('status', 'Staff account updated.');
    }

    /**
     * Remove a staff or admin account.
     */
    public function destroy(Credential $credential): RedirectResponse
    {
        $credential->delete();

        return redirect()->route('admin.users')->with('status', 'Staff account removed.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    /**
     * Change the username and password the signed-in admin uses on the dashboard login. The current
     * password must be given to confirm it is really the admin, and a blank new password keeps the old one.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($admin->getKey())],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ], [
            'username.unique' => 'That username is already taken. Choose a different one.',
            'current_password.required' => 'Enter your current password to save changes.',
            'current_password.current_password' => 'Your current password is not correct.',
            'password.min' => 'The new password must be at least 8 characters.',
        ]);

        $admin->name = $validated['username'];

        if (filled($validated['password'] ?? null)) {
            $admin->password = $validated['password'];
        }

        $admin->save();

        return redirect()->route('admin.users')->with('status', 'Admin login updated.');
    }
}

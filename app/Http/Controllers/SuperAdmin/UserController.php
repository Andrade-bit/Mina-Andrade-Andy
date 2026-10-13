<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformActivity;
use App\Models\Shop;
use App\Models\User;
use App\Support\AccountInvitations;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): View
    {
        return view('superadmin.users', ['users' => User::with('shop')->orderBy('name')->paginate(20), 'shops' => Shop::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:users,name'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'shop_id' => ['required', 'exists:shops,id'], 'role' => ['required', Rule::in(['owner', 'manager'])]]);
        DB::transaction(function () use ($request, $data): void {
            $user = User::create([...$data, 'password' => Str::random(64), 'is_active' => true]);
            app(AccountInvitations::class)->send($user);
            PlatformActivity::create(['user_id' => $request->user()->id, 'shop_id' => $user->shop_id, 'action' => 'user.created', 'description' => 'Shop login created: '.$user->name]);
        });

        return back()->with('status', 'Account invitation queued.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean'], 'role' => ['required', Rule::in($user->isSuperAdmin() ? ['superadmin'] : ['owner', 'manager'])], 'password' => ['prohibited']]);
        if ($user->id === $request->user()->id && ! $request->boolean('is_active')) {
            throw ValidationException::withMessages(['is_active' => 'You cannot disable your own superadmin account.']);
        }
        DB::transaction(function () use ($data, $request, $user): void {
            $current = User::lockForUpdate()->findOrFail($user->id);
            if ($current->role === 'owner' && (! $request->boolean('is_active') || $data['role'] !== 'owner') && ! User::where('shop_id', $current->shop_id)->where('role', 'owner')->where('is_active', true)->whereKeyNot($current->id)->exists()) {
                throw ValidationException::withMessages(['role' => 'Keep at least one active owner for this shop.']);
            }
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $current->update($data);
            PlatformActivity::create(['user_id' => $request->user()->id, 'shop_id' => $current->shop_id, 'action' => 'user.updated', 'description' => 'Login access updated: '.$current->name]);
        });

        return back()->with('status', 'Login access updated.');
    }
}

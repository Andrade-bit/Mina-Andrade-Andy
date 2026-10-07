<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List every staff/admin account.
     */
    public function index(Request $request): View
    {
        $accounts = Credential::query()->when($request->boolean('archived'), fn ($q) => $q->onlyTrashed());
        $role = in_array($request->query('role'), ['admin', 'assistant'], true) ? $request->query('role') : null;

        return view('admin.user-management', [
            'staff' => (clone $accounts)->when($role, fn ($q) => $q->where('role', $role))->withCount('salesTransactions')->orderBy('first_name')->get(),
            'counts' => (clone $accounts)->selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role'),
            'role' => $role,
        ]);
    }

    /**
     * Rules and messages shared by create and update. Every staff member needs their own PIN and their own
     * name, checked against archived accounts too so a PIN or name is never reused behind the scenes.
     *
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    private function validationRules(Request $request, ?Credential $credential = null): array
    {
        $sameName = fn ($name) => Credential::withTrashed()
            ->when($credential, fn ($q) => $q->whereKeyNot($credential->getKey()))
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower(trim((string) $request->input('first_name')))])
            ->whereRaw("LOWER(TRIM(COALESCE(middle_name, ''))) = ?", [mb_strtolower(trim((string) $request->input('middle_name')))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower(trim((string) $request->input('last_name')))])
            ->first();

        return [[
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255', function (string $attribute, mixed $value, Closure $fail) use ($sameName) {
                $existing = $sameName($value);

                if ($existing) {
                    $fail($existing->trashed()
                        ? 'A staff member with this name is already archived. Restore that account instead of creating a new one.'
                        : 'A staff member with this name already exists. Add a middle name or initial if they are different people.');
                }
            }],
            'role' => ['required', Rule::in(['admin', 'assistant'])],
            'passcode' => ['required', 'digits:4', Rule::unique('credentials', 'passcode')->ignore($credential?->id)],
        ], [
            'passcode.unique' => 'That PIN is already used by another staff member (archived accounts keep theirs). Choose a different 4-digit PIN.',
            'passcode.digits' => 'The PIN must be exactly 4 digits.',
        ]];
    }

    /**
     * Create a new staff or admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(...$this->validationRules($request));

        Credential::create($validated);

        return redirect()->route('admin.users')->with('status', 'Staff account created.');
    }

    /**
     * Update an existing staff or admin account.
     */
    public function update(Request $request, Credential $credential): RedirectResponse
    {
        $validated = $request->validate(...$this->validationRules($request, $credential));

        $credential->update($validated);

        return redirect()->route('admin.users')->with('status', 'Staff account updated.');
    }

    /**
     * Remove a staff or admin account.
     */
    public function destroy(Credential $credential): RedirectResponse
    {
        $credential->delete();

        return redirect()->route('admin.users')->with('status', 'Staff account archived.');
    }

    /**
     * Bring an archived staff account back.
     */
    public function restore(int $id): RedirectResponse
    {
        Credential::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.users', ['archived' => 1])->with('status', 'Staff account restored.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    /**
     * List every promo code.
     */
    public function index(): View
    {
        $promos = Promo::orderByDesc('active')->orderBy('code')->get();

        return view('admin.promos.index', [
            'promos' => $promos,
        ]);
    }

    /**
     * Create a new promo code.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promos,code'],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Promo::create([
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'reason' => $validated['reason'] ?? null,
            'active' => $request->boolean('active', true),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('admin.promos.index')->with('status', 'Promo created.');
    }

    /**
     * Update a promo code.
     */
    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('promos', 'code')->ignore($promo->id)],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $promo->update([
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'reason' => $validated['reason'] ?? null,
            'active' => $request->boolean('active'),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('admin.promos.index')->with('status', 'Promo updated.');
    }

    /**
     * Remove a promo code.
     */
    public function destroy(Promo $promo): RedirectResponse
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')->with('status', 'Promo removed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * List every registered supplier (e.g. the cups & straws vendor).
     */
    public function index(Request $request): View
    {
        $suppliers = Supplier::query()->when($request->boolean('archived'), fn ($q) => $q->onlyTrashed())->withCount('supplyPurchases')->orderBy('supplier_name')->get();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Show the form for registering a new supplier.
     */
    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    /**
     * Register a new supplier.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier added.');
    }

    /**
     * Update a supplier's details.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier updated.');
    }

    /**
     * Remove a supplier.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier archived.');
    }

    /**
     * Bring an archived supplier back.
     */
    public function restore(int $id): RedirectResponse
    {
        Supplier::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.suppliers.index', ['archived' => 1])->with('status', 'Supplier restored.');
    }
}

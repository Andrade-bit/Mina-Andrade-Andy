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
    public function index(): View
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
        ]);
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

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier removed.');
    }
}
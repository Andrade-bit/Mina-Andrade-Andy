<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\SalesTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesReportController extends Controller
{
    /**
     * View sales history, with optional filters for date range, payment
     * method, which Owner/Staff processed the sale, and free-text search
     * by order number or staff name.
     *
     * Restricted to admins — reachable either from the admin dashboard
     * (logged in via the users table) or from the POS terminal (unlocked
     * with an admin passcode) — assistants can sell but not view the log.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            $credentialId = $request->session()->get('pos_credential_id');
            $credential = $credentialId ? Credential::find($credentialId) : null;

            if (! $credential || $credential->role !== 'admin') {
                return redirect()->route('pos.login');
            }
        }

        $query = SalesTransaction::with('credential', 'promo', 'items.product', 'items.cupSize');

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('credential', function ($c) use ($search) {
                        $c->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->date('to'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        if ($request->filled('credential_id')) {
            $query->where('credential_id', $request->integer('credential_id'));
        }

        if (in_array($request->query('status'), ['completed', 'voided'], true)) {
            $query->where('status', $request->query('status'));
        }

        if (in_array($request->query('role'), ['admin', 'assistant'], true)) {
            $query->whereHas('credential', fn ($q) => $q->where('role', $request->query('role')));
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();

        return view('pos.transactions', [
            'transactions' => $transactions,
            'todaysSales' => SalesTransaction::whereDate('transaction_date', today())->where('status', '!=', 'voided')->sum('total_amount'),
            'todaysCount' => SalesTransaction::whereDate('transaction_date', today())->where('status', '!=', 'voided')->count(),
            'byOwnerCount' => SalesTransaction::where('status', '!=', 'voided')->whereHas('credential', fn ($q) => $q->where('role', 'admin'))->count(),
            'byStaffCount' => SalesTransaction::where('status', '!=', 'voided')->whereHas('credential', fn ($q) => $q->where('role', 'assistant'))->count(),
            'staffOptions' => Credential::orderBy('first_name')->get(),
        ]);
    }

    /**
     * Void a sale — admin-only, requires a typed reason and a live admin PIN
     * confirmation (re-entered here regardless of how the admin is already
     * signed in). Inventory is deliberately NOT auto-restored: voiding is a
     * pure accounting action, any stock correction is done separately.
     */
    public function void(Request $request, SalesTransaction $salesTransaction): RedirectResponse
    {
        if (! Auth::check()) {
            $credentialId = $request->session()->get('pos_credential_id');
            $credential = $credentialId ? Credential::find($credentialId) : null;

            if (! $credential || $credential->role !== 'admin') {
                return redirect()->route('pos.login');
            }
        }

        $validated = $request->validate([
            'void_reason' => ['required', 'string', 'max:500'],
            'admin_pin' => ['required', 'digits:4'],
        ]);

        if ($salesTransaction->status === 'voided') {
            return back()->withErrors(['void_reason' => 'This sale is already voided.']);
        }

        $admin = Credential::where('passcode', $validated['admin_pin'])->where('role', 'admin')->first();

        if (! $admin) {
            throw ValidationException::withMessages(['admin_pin' => 'Incorrect admin PIN.']);
        }

        $salesTransaction->update([
            'status' => 'voided',
            'void_reason' => $validated['void_reason'],
            'voided_by_credential_id' => $admin->id,
            'voided_at' => now(),
        ]);

        return back()->with('status', 'Sale #'.$salesTransaction->id.' voided.');
    }
}

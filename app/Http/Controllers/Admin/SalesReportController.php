<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\SalesTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $query = SalesTransaction::with('credential', 'items.product', 'items.cupSize');

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

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();

        return view('pos.transactions', [
            'transactions' => $transactions,
            'todaysSales' => SalesTransaction::whereDate('transaction_date', today())->sum('total_amount'),
            'todaysCount' => SalesTransaction::whereDate('transaction_date', today())->count(),
            'byOwnerCount' => SalesTransaction::whereHas('credential', fn ($q) => $q->where('role', 'admin'))->count(),
            'byStaffCount' => SalesTransaction::whereHas('credential', fn ($q) => $q->where('role', 'assistant'))->count(),
            'staffOptions' => Credential::orderBy('first_name')->get(),
        ]);
    }
}

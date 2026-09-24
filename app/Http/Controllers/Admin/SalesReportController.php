<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /**
     * View sales history, with optional filters for date range, payment
     * method, and which Owner/Staff processed the sale.
     */
    public function index(Request $request): View
    {
        $query = SalesTransaction::with('credential', 'items.product', 'items.cupSize');

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

        $transactions = $query->latest('transaction_date')->paginate(30)->withQueryString();

        return view('pos.transactions', [
            'transactions' => $transactions,
            'todaysSales' => SalesTransaction::whereDate('transaction_date', today())->sum('total_amount'),
            'todaysCount' => SalesTransaction::whereDate('transaction_date', today())->count(),
            'byOwnerCount' => SalesTransaction::whereHas('credential', fn ($q) => $q->where('role', 'admin'))->count(),
            'byStaffCount' => SalesTransaction::whereHas('credential', fn ($q) => $q->where('role', 'assistant'))->count(),
        ]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Support\ShopDates;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * List recorded expenses, most recent first.
     */
    public function index(Request $request): View
    {
        ShopDates::validate($request);
        $from = $request->input('from');
        $to = $request->input('to');
        $filtered = $request->filled('from') || $request->filled('to');
        $query = Expense::query()->when($request->boolean('archived'), fn ($q) => $q->onlyTrashed())
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to));
        $total = clone $query;
        if (! $filtered) {
            $total->whereMonth('expense_date', now('Asia/Manila')->month)
                ->whereYear('expense_date', now('Asia/Manila')->year);
        }

        return view('admin.expenses.index', [
            'expenses' => $query->with('expenseCategory', 'supplyPurchase')->latest('expense_date')->latest('id')->paginate(30)->withQueryString(),
            'totalThisMonth' => $total->sum('amount'),
            'summaryLabel' => $filtered ? 'Selected dates' : 'This month',
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Show the form for recording a new expense.
     */
    public function create(): View
    {
        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('admin.expenses.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Record a new expense.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        Expense::create($validated);

        return redirect()->route('admin.expenses.index')->with('status', 'Expense recorded.');
    }

    /**
     * Remove an expense record.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('status', 'Expense archived.');
    }

    /**
     * Bring an archived expense back.
     */
    public function restore(int $id): RedirectResponse
    {
        Expense::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('admin.expenses.index', ['archived' => 1])->with('status', 'Expense restored.');
    }
}

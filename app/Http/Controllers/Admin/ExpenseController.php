<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * List recorded expenses, most recent first.
     */
    public function index(): View
    {
        $expenses = Expense::with('expenseCategory')->latest('expense_date')->paginate(30);

        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'totalThisMonth' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
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

        return redirect()->route('admin.expenses.index')->with('status', 'Expense removed.');
    }
}
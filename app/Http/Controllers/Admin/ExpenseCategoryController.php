<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * List every expense category (e.g. Rent, Utilities, Supplies).
     */
    public function index(): View
    {
        $categories = ExpenseCategory::withCount('expenses')->orderBy('category_name')->get();

        return view('admin.expense-categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Create a new expense category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('admin.expense-categories.index')->with('status', 'Category created.');
    }

    /**
     * Update an expense category.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('admin.expense-categories.index')->with('status', 'Category updated.');
    }

    /**
     * Delete an expense category.
     */
    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->delete();

        return redirect()->route('admin.expense-categories.index')->with('status', 'Category removed.');
    }
}
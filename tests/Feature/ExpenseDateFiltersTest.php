<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExpenseDateFiltersTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_date_range_is_inclusive_and_total_covers_all_pages(): void
    {
        $category = ExpenseCategory::create(['category_name' => 'Rent']);
        for ($i = 0; $i < 31; $i++) {
            Expense::create(['expense_category_id' => $category->id, 'description' => 'Inside', 'amount' => 10, 'expense_date' => $i === 0 ? '2026-10-01' : '2026-10-12', 'payment_method' => 'Cash']);
        }
        Expense::create(['expense_category_id' => $category->id, 'description' => 'Outside', 'amount' => 900, 'expense_date' => '2026-10-13', 'payment_method' => 'Cash']);
        $this->actingAs(User::factory()->create())->get(route('admin.expenses.index', ['from' => '2026-10-01', 'to' => '2026-10-12']))
            ->assertOk()->assertViewHas('totalThisMonth', fn ($value) => (float) $value === 310.0)
            ->assertViewHas('expenses', fn ($rows) => $rows->total() === 31 && $rows->count() === 30 && str_contains($rows->url(2), 'from=2026-10-01'));
    }

    public function test_archive_filter_and_open_ended_dates_remain_independent(): void
    {
        $category = ExpenseCategory::create(['category_name' => 'Rent']);
        $expense = Expense::create(['expense_category_id' => $category->id, 'description' => 'Archived', 'amount' => 25, 'expense_date' => '2026-10-12', 'payment_method' => 'Cash']);
        $expense->delete();
        $this->actingAs(User::factory()->create())->get(route('admin.expenses.index', ['from' => '2026-10-12']))
            ->assertOk()->assertViewHas('totalThisMonth', fn ($value) => (float) $value === 0.0);
        $this->get(route('admin.expenses.index', ['to' => '2026-10-12', 'archived' => 1]))
            ->assertOk()->assertViewHas('totalThisMonth', fn ($value) => (float) $value === 25.0)
            ->assertViewHas('expenses', fn ($rows) => $rows->total() === 1);
        $this->get(route('admin.expenses.index', ['from' => '2026-10-13', 'to' => '2026-10-12']))->assertSessionHasErrors('to');
        $this->get(route('admin.expenses.index', ['from' => 'invalid']))->assertSessionHasErrors('from');
    }
}

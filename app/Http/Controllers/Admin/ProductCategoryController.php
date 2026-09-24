<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * List every menu category (e.g. Hot Coffee, Iced Coffee, Fruit Juice).
     */
    public function index(): View
    {
        $categories = ProductCategory::withCount('products')->orderBy('category_name')->get();

        return view('admin.product-categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Create a new menu category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
        ]);

        ProductCategory::create($validated);

        return redirect()->route('admin.product-categories.index')->with('status', 'Category created.');
    }

    /**
     * Update a menu category.
     */
    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
        ]);

        $productCategory->update($validated);

        return redirect()->route('admin.product-categories.index')->with('status', 'Category updated.');
    }

    /**
     * Delete a menu category.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->delete();

        return redirect()->route('admin.product-categories.index')->with('status', 'Category removed.');
    }
}
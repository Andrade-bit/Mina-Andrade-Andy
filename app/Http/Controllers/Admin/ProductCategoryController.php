<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pos\TerminalController;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * List every menu category (e.g. Hot Coffee, Iced Coffee, Fruit Juice).
     */
    public function index(Request $request): View
    {
        $categories = ProductCategory::query()->when($request->boolean('archived'), fn ($q) => $q->onlyTrashed())->withCount('products')->orderBy('category_name')->get();

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
        $holders = TerminalController::openCartsHolding($productCategory->products()->pluck('id')->all());

        if ($holders->isNotEmpty()) {
            return redirect()->route('admin.product-categories.index')->with('error', TerminalController::openCartMessage("the '{$productCategory->category_name}' category", $holders));
        }

        $productCategory->products()->delete();
        $productCategory->delete();

        return redirect()->route('admin.product-categories.index')->with('status', 'Category archived.');
    }

    /**
     * Bring an archived category back.
     */
    public function restore(int $id): RedirectResponse
    {
        $category = ProductCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        // Bring back the products that were archived along with this category.
        Product::onlyTrashed()->where('product_category_id', $category->id)->where('deleted_at', '>=', $category->deleted_at)->restore();

        return redirect()->route('admin.product-categories.index', ['archived' => 1])->with('status', 'Category restored.');
    }
}

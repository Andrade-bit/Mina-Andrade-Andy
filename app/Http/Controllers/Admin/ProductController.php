<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List every menu item with its category.
     */
    public function index(): View
    {
        $products = Product::with('productCategory')->orderBy('product_name')->get();

        return view('admin.products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for adding a new menu item.
     */
    public function create(): View
    {
        $categories = ProductCategory::orderBy('category_name')->get();

        return view('admin.products.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Add a new menu item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    /**
     * Show the form for editing a menu item.
     */
    public function edit(Product $product): View
    {
        $categories = ProductCategory::orderBy('category_name')->get();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update a menu item.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    /**
     * Remove a menu item.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product removed.');
    }
}
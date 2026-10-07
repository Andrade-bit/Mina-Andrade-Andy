<?php

namespace App\Http\Controllers;

use App\Models\CupSize;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    /**
     * The public home page: the menu comes straight from Products, so
     * archived drinks disappear and new ones show up on their own.
     */
    public function __invoke(): View
    {
        $cupSizes = CupSize::orderBy('price')->get();

        $groups = ProductCategory::with(['products' => fn ($query) => $query->with('cupSizePrices')->orderBy('product_name')])
            ->orderBy('id')
            ->get()
            ->filter(fn (ProductCategory $category) => $category->products->isNotEmpty())
            ->values()
            ->map(fn (ProductCategory $category) => [
                'name' => $category->category_name,
                'slug' => Str::slug($category->category_name),
                'items' => $category->products->map(fn (Product $product) => $this->menuItem($product, $cupSizes))->all(),
            ]);

        $mocha = Product::with('cupSizePrices')->where('product_name', 'like', '%mocha%')->first();

        $lowestPrice = $groups
            ->flatMap(fn (array $group) => $group['items'])
            ->flatMap(fn (array $item) => collect($item['sizes'])->pluck('amount'))
            ->min();

        return view('landing', [
            'groups' => $groups,
            'signature' => $mocha ? $this->menuItem($mocha, $cupSizes) : null,
            'lowestPrice' => $lowestPrice !== null ? $this->formatPrice($lowestPrice) : null,
            'story' => config('landing.story'),
            'address' => config('landing.address'),
            'hours' => config('landing.hours'),
            'contact' => config('landing.contact'),
        ]);
    }

    /**
     * @param  Collection<int, CupSize>  $cupSizes
     * @return array{name: string, image: ?string, sizes: list<array{label: string, amount: float, price: string}>}
     */
    private function menuItem(Product $product, Collection $cupSizes): array
    {
        $sizes = $product->effectiveCupSizes($cupSizes);

        // "One Size" is only worth showing when it is the only choice.
        $multiple = $sizes->filter(fn ($size) => strcasecmp($size->size_name, 'One Size') !== 0);
        $sizes = $multiple->isNotEmpty() ? $multiple : $sizes;

        return [
            'name' => $product->product_name,
            'image' => $product->displayImageUrl(),
            'sizes' => $sizes->map(fn ($size) => [
                'label' => match (strtolower($size->size_name)) {
                    'small' => 'S',
                    'medium' => 'M',
                    'large' => 'L',
                    default => $size->size_name,
                },
                'amount' => (float) $size->price,
                'price' => $this->formatPrice((float) $size->price),
            ])->values()->all(),
        ];
    }

    private function formatPrice(float $amount): string
    {
        return $amount == floor($amount) ? number_format($amount, 0) : number_format($amount, 2);
    }
}

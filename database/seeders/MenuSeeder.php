<?php

namespace Database\Seeders;

use App\Models\CupSize;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the menu: categories, products, and cup sizes.
     *
     * Hot Coffee is one flat price (uses the "One Size" cup size). Every
     * other category is priced by Small/Medium/Large, matching the shop's
     * real pricing tiers.
     */
    public function run(): void
    {
        CupSize::updateOrCreate(['size_name' => 'One Size'], ['price' => 35]);
        CupSize::updateOrCreate(['size_name' => 'Small'], ['price' => 35]);
        CupSize::updateOrCreate(['size_name' => 'Medium'], ['price' => 40]);
        CupSize::updateOrCreate(['size_name' => 'Large'], ['price' => 50]);

        $menu = [
            'Hot Coffee' => ['Hot Americano', 'Hot Cafe Latte', 'Hot Cappuccino', 'Hot Spanish Latte', 'Hot Caramel Macchiato'],
            'Iced Coffee' => ['Iced Americano', 'Iced Cafe Latte', 'Iced Cappuccino', 'Iced Spanish Latte', 'Iced Caramel Macchiato'],
            'Non-Coffee' => ['Matcha Latte', 'Chocolate', 'Strawberry Milk'],
            'Fruit Juice' => ['Mango Juice', 'Lemonade', 'Blue Lemonade'],
        ];

        foreach ($menu as $categoryName => $productNames) {
            $category = ProductCategory::updateOrCreate(['category_name' => $categoryName]);

            foreach ($productNames as $productName) {
                Product::updateOrCreate(
                    ['product_name' => $productName],
                    ['product_category_id' => $category->id]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CupSize;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Seed starter stock: ingredients the admin procures directly, and the
     * cup/straw supplies ordered through the packaging supplier.
     */
    public function run(): void
    {
        // Stock is counted in ml, g or pcs; "bought" is the unit it is purchased in and "size" how many base units it holds.
        $ingredients = [
            ['name' => 'Coffee Beans', 'unit' => 'g', 'bought' => 'kg', 'size' => 1000, 'current_quantity' => 25000, 'reorder_level' => 10000],
            ['name' => 'Fresh Milk', 'unit' => 'ml', 'bought' => 'L', 'size' => 1000, 'current_quantity' => 40000, 'reorder_level' => 15000],
            ['name' => 'Condensed Milk', 'unit' => 'g', 'bought' => 'can', 'size' => 390, 'current_quantity' => 11700, 'reorder_level' => 4680],
            ['name' => 'Caramel Syrup', 'unit' => 'ml', 'bought' => 'bottle', 'size' => 750, 'current_quantity' => 6000, 'reorder_level' => 3750],
            ['name' => 'Matcha Powder', 'unit' => 'g', 'bought' => 'kg', 'size' => 1000, 'current_quantity' => 4000, 'reorder_level' => 3000],
            ['name' => 'Chocolate Powder', 'unit' => 'g', 'bought' => 'kg', 'size' => 1000, 'current_quantity' => 12000, 'reorder_level' => 5000],
            ['name' => 'Mango Puree', 'unit' => 'ml', 'bought' => 'L', 'size' => 1000, 'current_quantity' => 10000, 'reorder_level' => 6000],
            ['name' => 'Lemon Juice Concentrate', 'unit' => 'ml', 'bought' => 'L', 'size' => 1000, 'current_quantity' => 6000, 'reorder_level' => 4000],
            ['name' => 'White Sugar', 'unit' => 'g', 'bought' => 'kg', 'size' => 1000, 'current_quantity' => 20000, 'reorder_level' => 8000],
            ['name' => 'Strawberry Syrup', 'unit' => 'ml', 'bought' => 'bottle', 'size' => 750, 'current_quantity' => 5250, 'reorder_level' => 3750],
        ];

        foreach ($ingredients as $ingredient) {
            InventoryItem::updateOrCreate(
                ['name' => $ingredient['name']],
                [
                    'type' => 'ingredient',
                    'unit' => $ingredient['unit'],
                    'secondary_unit' => $ingredient['bought'],
                    'conversion_factor' => $ingredient['size'],
                    'current_quantity' => $ingredient['current_quantity'],
                    'reorder_level' => $ingredient['reorder_level'],
                    'status' => 'active',
                ]
            );
        }

        $supplies = [
            ['name' => 'Small Cups (12oz)', 'unit' => 'pcs', 'current_quantity' => 250, 'reorder_level' => 100, 'cup_size' => 'Small'],
            ['name' => 'Medium Cups (16oz)', 'unit' => 'pcs', 'current_quantity' => 180, 'reorder_level' => 100, 'cup_size' => 'Medium'],
            ['name' => 'Large Cups (22oz)', 'unit' => 'pcs', 'current_quantity' => 90, 'reorder_level' => 100, 'cup_size' => 'Large'],
            ['name' => 'Plastic Straws', 'unit' => 'pcs', 'current_quantity' => 400, 'reorder_level' => 150, 'cup_size' => null],
        ];

        foreach ($supplies as $supply) {
            $item = InventoryItem::updateOrCreate(
                ['name' => $supply['name']],
                [
                    'type' => 'supply',
                    'unit' => $supply['unit'],
                    'current_quantity' => $supply['current_quantity'],
                    'reorder_level' => $supply['reorder_level'],
                    'status' => 'active',
                ]
            );

            if ($supply['cup_size']) {
                CupSize::where('size_name', $supply['cup_size'])->update(['inventory_item_id' => $item->id]);
            }
        }
    }
}

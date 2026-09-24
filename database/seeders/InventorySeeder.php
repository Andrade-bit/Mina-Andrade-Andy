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
        $ingredients = [
            ['name' => 'Coffee Beans', 'unit' => 'kg', 'current_quantity' => 25, 'reorder_level' => 10],
            ['name' => 'Fresh Milk', 'unit' => 'liters', 'current_quantity' => 40, 'reorder_level' => 15],
            ['name' => 'Condensed Milk', 'unit' => 'cans', 'current_quantity' => 30, 'reorder_level' => 12],
            ['name' => 'Caramel Syrup', 'unit' => 'bottles', 'current_quantity' => 8, 'reorder_level' => 5],
            ['name' => 'Matcha Powder', 'unit' => 'kg', 'current_quantity' => 4, 'reorder_level' => 3],
            ['name' => 'Chocolate Powder', 'unit' => 'kg', 'current_quantity' => 12, 'reorder_level' => 5],
            ['name' => 'Mango Puree', 'unit' => 'liters', 'current_quantity' => 10, 'reorder_level' => 6],
            ['name' => 'Lemon Juice Concentrate', 'unit' => 'liters', 'current_quantity' => 6, 'reorder_level' => 4],
            ['name' => 'White Sugar', 'unit' => 'kg', 'current_quantity' => 20, 'reorder_level' => 8],
            ['name' => 'Strawberry Syrup', 'unit' => 'bottles', 'current_quantity' => 7, 'reorder_level' => 5],
        ];

        foreach ($ingredients as $ingredient) {
            InventoryItem::updateOrCreate(
                ['name' => $ingredient['name']],
                [
                    'type' => 'ingredient',
                    'unit' => $ingredient['unit'],
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

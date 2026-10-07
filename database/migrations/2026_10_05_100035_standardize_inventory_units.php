<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock and recipes are now counted in ml, g or pcs only. Items on a free-text unit whose size is
     * certain (kg, liters...) are converted here, along with every quantity recorded against them.
     * Items on units with no fixed size (bottles, cans...) are left alone; set them from the item's edit page.
     */
    public function up(): void
    {
        Schema::table('supply_purchase_items', function (Blueprint $table) {
            $table->decimal('unit_cost', 12, 4)->change();
        });

        $conversions = [
            'kg' => ['g', 1000, 'kg'],
            'kilogram' => ['g', 1000, 'kg'],
            'kilograms' => ['g', 1000, 'kg'],
            'liter' => ['ml', 1000, 'L'],
            'liters' => ['ml', 1000, 'L'],
            'litre' => ['ml', 1000, 'L'],
            'litres' => ['ml', 1000, 'L'],
            'l' => ['ml', 1000, 'L'],
        ];

        DB::transaction(function () use ($conversions) {
            foreach (DB::table('inventory_items')->get() as $item) {
                $match = $conversions[mb_strtolower(trim((string) $item->unit))] ?? null;

                if (! $match) {
                    continue;
                }

                [$baseUnit, $factor, $purchaseUnit] = $match;

                DB::table('inventory_items')->where('id', $item->id)->update([
                    'unit' => $baseUnit,
                    'secondary_unit' => $purchaseUnit,
                    'conversion_factor' => $factor,
                    'current_quantity' => DB::raw('current_quantity * '.$factor),
                    'reorder_level' => DB::raw('reorder_level * '.$factor),
                ]);

                DB::table('inventory_transactions')->where('inventory_item_id', $item->id)
                    ->update(['quantity' => DB::raw('quantity * '.$factor)]);

                DB::table('supply_purchase_items')->where('inventory_item_id', $item->id)
                    ->update([
                        'quantity' => DB::raw('quantity * '.$factor),
                        'unit_cost' => DB::raw('unit_cost / '.$factor),
                    ]);

                $ingredientIds = DB::table('ingredients')->where('inventory_item_id', $item->id)->pluck('id');

                DB::table('product_ingredients')->whereIn('ingredient_id', $ingredientIds)
                    ->update(['quantity_required' => DB::raw('quantity_required * '.$factor)]);
            }
        });
    }

    /**
     * Converted quantities can't be told apart from ones entered later, so this is not reversible;
     * restore the pre-migration backup instead.
     */
    public function down(): void
    {
        //
    }
};

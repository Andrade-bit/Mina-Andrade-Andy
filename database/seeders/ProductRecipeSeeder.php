<?php

namespace Database\Seeders;

use App\Models\CupSize;
use App\Models\Ingredient;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductIngredientSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRecipeSeeder extends Seeder
{
    /**
     * Cup volumes in ml. The 12oz / 16oz / 22oz cups the Cup Sizes are linked to. One Size is the hot cup,
     * taken as the 12oz cup (it sells at the Small price); change it on the Cup Sizes page if yours differs.
     *
     * @var array<string, int>
     */
    private const CUP_VOLUMES = ['One Size' => 355, 'Small' => 355, 'Medium' => 480, 'Large' => 650];

    /**
     * The amounts below are for this cup. The other sizes get their own amount, worked out from the cup volumes
     * (Small 355 ml is about 74% of Medium, Large 650 ml about 135%) and saved per size, so each one can be
     * adjusted on the product page afterwards.
     */
    private const RECIPE_SIZE = 'Medium';

    /**
     * The unit each ingredient must be counted in for the amounts below to mean what they say.
     *
     * @var array<string, string>
     */
    private const STOCK_UNITS = [
        'Coffee Beans' => 'g',
        'Fresh Milk' => 'ml',
        'Condensed Milk' => 'g',
        'Caramel Syrup' => 'ml',
        'Matcha Powder' => 'g',
        'Chocolate Powder' => 'g',
        'Mango Puree' => 'ml',
        'Lemon Juice Concentrate' => 'ml',
        'White Sugar' => 'g',
        'Strawberry Syrup' => 'ml',
    ];

    /**
     * Items still counted in bottles or cans. Their size is not something the system can know, so it is set here:
     * these are the sizes you chose (a 750 ml bottle of syrup, a 390 g can of condensed milk). They are converted
     * to ml or g, with their stock and history, the same way Edit does it. Change the size on Inventory > Edit
     * later and future purchases follow it.
     *
     * @var array<string, array{legacy: string, unit: string, purchase_unit: string, size: int}>
     */
    private const PACKED_ITEMS = [
        'Caramel Syrup' => ['legacy' => 'bottles', 'unit' => 'ml', 'purchase_unit' => 'bottle', 'size' => 750],
        'Condensed Milk' => ['legacy' => 'cans', 'unit' => 'g', 'purchase_unit' => 'can', 'size' => 390],
    ];

    /**
     * The migration that turns kg and liters into g and ml. It also widens the purchase cost column that
     * converting bottles and cans relies on, so it has to have run first.
     */
    private const UNITS_MIGRATION = '2026_10_05_100035_standardize_inventory_units';

    /**
     * Ingredients that may be missing (for example archived) without stopping the rest: that line is skipped.
     *
     * @var array<int, string>
     */
    private const OPTIONAL_ITEMS = ['Strawberry Syrup'];

    /**
     * Amount of each ingredient in one Medium (480 ml) drink, in the ingredient's stock unit. A double shot is
     * 18 g of beans. Ice and water are not tracked in Inventory, so they are not listed.
     *
     * @var array<string, array<string, int>>
     */
    private const RECIPES = [
        'Hot Americano' => ['Coffee Beans' => 18],
        'Hot Cafe Latte' => ['Coffee Beans' => 18, 'Fresh Milk' => 300],
        'Hot Cappuccino' => ['Coffee Beans' => 18, 'Fresh Milk' => 200],
        'Hot Spanish Latte' => ['Coffee Beans' => 18, 'Fresh Milk' => 220, 'Condensed Milk' => 45],
        'Hot Caramel Macchiato' => ['Coffee Beans' => 18, 'Fresh Milk' => 250, 'Caramel Syrup' => 30],
        'hazelnuts' => ['Coffee Beans' => 18, 'Fresh Milk' => 250, 'Caramel Syrup' => 30],
        'Iced Americano' => ['Coffee Beans' => 18],
        'Iced Cafe Latte' => ['Coffee Beans' => 18, 'Fresh Milk' => 200],
        'Iced Cappuccino' => ['Coffee Beans' => 18, 'Fresh Milk' => 180],
        'Iced Spanish Latte' => ['Coffee Beans' => 18, 'Fresh Milk' => 160, 'Condensed Milk' => 45],
        'Iced Caramel Macchiato' => ['Coffee Beans' => 18, 'Fresh Milk' => 200, 'Caramel Syrup' => 30],
        'Matcha Latte' => ['Matcha Powder' => 6, 'Fresh Milk' => 220, 'White Sugar' => 15],
        'Chocolate' => ['Chocolate Powder' => 30, 'Fresh Milk' => 250, 'White Sugar' => 10],
        'Strawberry Milk' => ['Strawberry Syrup' => 40, 'Fresh Milk' => 220],
        'Mango Juice' => ['Mango Puree' => 150, 'White Sugar' => 15],
        'Lemonade' => ['Lemon Juice Concentrate' => 30, 'White Sugar' => 25],
        'Blue Lemonade' => ['Lemon Juice Concentrate' => 30, 'White Sugar' => 25],
    ];

    /**
     * Give every product its ingredients, scaled by cup size. Nothing is written unless every ingredient is
     * counted in the unit the amounts assume, so a recipe can never deduct "30 liters" by mistake.
     */
    public function run(): void
    {
        if (! DB::table('migrations')->where('migration', self::UNITS_MIGRATION)->exists()) {
            $this->command?->error('Recipes were not added. Run php artisan migrate first: it converts your kg and liter stock to g and ml.');

            return;
        }

        $items = InventoryItem::whereIn('name', array_keys(self::STOCK_UNITS))->get()->keyBy('name');

        $problems = [];

        foreach (self::STOCK_UNITS as $name => $unit) {
            $item = $items->get($name);

            if (! $item) {
                if (! in_array($name, self::OPTIONAL_ITEMS, true)) {
                    $problems[] = "{$name} is not in Inventory.";
                }

                continue;
            }

            $packed = self::PACKED_ITEMS[$name] ?? null;
            $willBeConverted = $packed && mb_strtolower(trim($item->unit)) === $packed['legacy'];

            if ($item->unit !== $unit && ! $willBeConverted) {
                $problems[] = "{$name} is counted in \"{$item->unit}\" but must be \"{$unit}\". Use Edit on the item in Inventory to set its unit.";
            }
        }

        if ($problems !== []) {
            $this->command?->error('Recipes were not added. Fix these first, then run this seeder again:');

            foreach ($problems as $problem) {
                $this->command?->line(' - '.$problem);
            }

            return;
        }

        DB::transaction(function () use (&$items) {
            $items = $this->standardizePackedItems($items);
            $this->setCupVolumes();
            $sizeIds = CupSize::whereIn('size_name', array_keys(self::CUP_VOLUMES))->pluck('id', 'size_name');

            foreach (self::RECIPES as $productName => $recipe) {
                $product = Product::where('product_name', $productName)->first();

                if (! $product) {
                    $this->command?->warn("Skipped {$productName}: no such product.");

                    continue;
                }

                $syncData = [];
                $sizeRows = [];

                foreach ($recipe as $itemName => $amount) {
                    $item = $items->get($itemName);

                    if (! $item) {
                        $this->command?->warn("{$productName}: left out {$itemName} (not in Inventory, perhaps archived).");

                        continue;
                    }

                    $ingredient = Ingredient::firstOrCreate(['inventory_item_id' => $item->id]);
                    $syncData[$ingredient->id] = ['quantity_required' => $amount];

                    foreach ($sizeIds as $sizeName => $sizeId) {
                        $sizeRows[] = [
                            'product_id' => $product->id,
                            'ingredient_id' => $ingredient->id,
                            'cup_size_id' => $sizeId,
                            'quantity_required' => $this->amountForSize($amount, self::CUP_VOLUMES[$sizeName]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                $product->ingredients()->sync($syncData);
                $product->ingredientSizeAmounts()->delete();
                ProductIngredientSize::insert($sizeRows);
                $this->command?->info("{$productName}: ".count($syncData).' ingredients.');
            }
        });
    }

    /**
     * Convert bottle and can items to ml and g, and hand back the items as they are now.
     *
     * @param  Collection<string, InventoryItem>  $items  keyed by name
     * @return Collection<string, InventoryItem>
     */
    private function standardizePackedItems(Collection $items): Collection
    {
        foreach (self::PACKED_ITEMS as $name => $pack) {
            $item = $items->get($name);

            if (! $item || mb_strtolower(trim($item->unit)) !== $pack['legacy']) {
                continue;
            }

            $item->convertStockUnit($pack['size']);
            $item->update(['unit' => $pack['unit'], 'secondary_unit' => $pack['purchase_unit'], 'conversion_factor' => $pack['size']]);

            $this->command?->info("{$name}: now counted in {$pack['unit']} (1 {$pack['purchase_unit']} = {$pack['size']} {$pack['unit']}).");
        }

        return InventoryItem::whereIn('name', array_keys(self::STOCK_UNITS))->get()->keyBy('name');
    }

    /**
     * The Medium amount scaled to a cup volume: whole numbers from 10 up, halves below that.
     */
    private function amountForSize(float|int $mediumAmount, int $volume): float
    {
        $amount = $mediumAmount * $volume / self::CUP_VOLUMES[self::RECIPE_SIZE];

        return $amount >= 10 ? (float) round($amount) : round($amount * 2) / 2;
    }

    private function setCupVolumes(): void
    {
        CupSize::query()->update(['is_recipe_size' => false]);

        foreach (self::CUP_VOLUMES as $sizeName => $volume) {
            CupSize::where('size_name', $sizeName)->update([
                'volume_ml' => $volume,
                'is_recipe_size' => $sizeName === self::RECIPE_SIZE,
            ]);
        }
    }
}

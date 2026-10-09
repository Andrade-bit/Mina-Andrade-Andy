<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The only units stock and recipes are counted in, so every amount in the system is comparable.
     *
     * @var array<string, string>
     */
    public const BASE_UNITS = [
        'ml' => 'Milliliters (ml)',
        'g' => 'Grams (g)',
        'pcs' => 'Pieces (pcs)',
    ];

    /**
     * Units an item can be bought in, per base unit. A number is the fixed size in the base unit and is
     * converted automatically; null means the pack size varies, so it is entered per item (1 bottle = ? ml).
     *
     * @var array<string, array<string, float|int|null>>
     */
    public const PURCHASE_UNITS = [
        'ml' => ['L' => 1000, 'gallon' => 3785.41, 'bottle' => null, 'can' => null, 'carton' => null, 'pack' => null],
        'g' => ['kg' => 1000, 'lb' => 453.592, 'sack' => null, 'pack' => null, 'can' => null, 'box' => null],
        'pcs' => ['dozen' => 12, 'pack' => null, 'box' => null, 'case' => null],
    ];

    /**
     * Free-text units used before units were fixed, mapped to [base unit, how many base units in one].
     * Anything not listed (bottles, cans...) has no known size and must be set by hand.
     *
     * @var array<string, array{0: string, 1: int}>
     */
    public const LEGACY_UNITS = [
        'kg' => ['g', 1000],
        'kilogram' => ['g', 1000],
        'kilograms' => ['g', 1000],
        'g' => ['g', 1],
        'gram' => ['g', 1],
        'grams' => ['g', 1],
        'l' => ['ml', 1000],
        'liter' => ['ml', 1000],
        'liters' => ['ml', 1000],
        'litre' => ['ml', 1000],
        'litres' => ['ml', 1000],
        'ml' => ['ml', 1],
        'pc' => ['pcs', 1],
        'pcs' => ['pcs', 1],
        'piece' => ['pcs', 1],
        'pieces' => ['pcs', 1],
    ];

    protected $fillable = [
        'name',
        'type',
        'image',
        'unit',
        'secondary_unit',
        'conversion_factor',
        'current_quantity',
        'reorder_level',
        'status',
    ];

    /**
     * A displayable URL for this item's photo, if one was uploaded.
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'conversion_factor' => 'decimal:4',
    ];

    /**
     * The fixed size of a purchase unit in its base unit (1 L = 1000 ml), or null when it varies per item.
     */
    public static function fixedPurchaseFactor(string $baseUnit, string $purchaseUnit): ?float
    {
        $factor = self::PURCHASE_UNITS[$baseUnit][$purchaseUnit] ?? null;

        return $factor === null ? null : (float) $factor;
    }

    /**
     * Whether this item is already counted in one of the fixed base units.
     */
    public function hasStandardUnit(): bool
    {
        return array_key_exists($this->unit, self::BASE_UNITS);
    }

    /**
     * Re-express everything recorded against this item in a new stock unit, where one old unit is $factor new
     * units: stock on hand, reorder level, movement history, purchase lines (with their cost per unit) and the
     * amounts recipes use. The caller sets the new unit itself.
     */
    public function convertStockUnit(float $factor): void
    {
        $factorSql = number_format($factor, 8, '.', '');

        $this->current_quantity = round((float) $this->current_quantity * $factor, 2);
        $this->reorder_level = round((float) $this->reorder_level * $factor, 2);
        $this->save();

        $this->batches()->update([
            'quantity' => DB::raw("quantity * {$factorSql}"),
            'remaining_quantity' => DB::raw("remaining_quantity * {$factorSql}"),
        ]);

        InventoryTransaction::where('inventory_item_id', $this->id)
            ->update(['quantity' => DB::raw("quantity * {$factorSql}")]);

        DB::table('supply_purchase_items')->where('inventory_item_id', $this->id)
            ->update([
                'quantity' => DB::raw("quantity * {$factorSql}"),
                'unit_cost' => DB::raw("unit_cost / {$factorSql}"),
            ]);

        DB::table('product_ingredients')
            ->whereIn('ingredient_id', Ingredient::where('inventory_item_id', $this->id)->pluck('id'))
            ->update(['quantity_required' => DB::raw("quantity_required * {$factorSql}")]);
    }

    /**
     * What the purchase and stock-out forms need to know about this item, as plain data for their JavaScript.
     *
     * @return array{id: int, name: string, unit: string, secondary_unit: ?string, conversion_factor: ?string, fixed_size: bool}
     */
    public function toFormOption(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'secondary_unit' => $this->secondary_unit,
            'conversion_factor' => $this->conversion_factor,
            'fixed_size' => $this->secondary_unit
                && self::fixedPurchaseFactor($this->unit, $this->secondary_unit) !== null,
        ];
    }

    /**
     * Where an item still on a free-text unit should land, e.g. kg -> [g, 1000]; null when its size is unknown.
     *
     * @return array{0: string, 1: int}|null
     */
    public function legacyConversion(): ?array
    {
        return self::LEGACY_UNITS[mb_strtolower(trim((string) $this->unit))] ?? null;
    }

    /**
     * Current stock in the unit it is bought in, e.g. "35 kg", or null when no purchase unit is set.
     */
    public function stockInPurchaseUnit(): ?string
    {
        if (! $this->secondary_unit || (float) $this->conversion_factor <= 0) {
            return null;
        }

        $amount = round((float) $this->current_quantity / (float) $this->conversion_factor, 2);

        return rtrim(rtrim(number_format($amount, 2), '0'), '.').' '.$this->secondary_unit;
    }

    /**
     * How many active products use this item in their recipe (needs ingredients.products loaded).
     */
    public function recipeProductCount(): int
    {
        return $this->ingredients->flatMap(fn ($ingredient) => $ingredient->products)->pluck('id')->unique()->count();
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function usableQuantity(): float
    {
        $expired = $this->relationLoaded('batches')
            ? $this->batches->filter(fn (InventoryBatch $batch) => $batch->daysUntilExpiry() !== null && $batch->daysUntilExpiry() < 0)->sum('remaining_quantity')
            : $this->batches()->where('expires_at', '<', InventoryBatch::expiryToday()->toDateString())->sum('remaining_quantity');

        return max(0, round((float) $this->current_quantity - (float) $expired, 2));
    }

    /**
     * Deduct earliest-expiring stock first. The caller holds this item's row lock inside a transaction.
     * Undated stock (including stock held before batch tracking) is used after dated stock.
     */
    public function consumeStock(float $quantity, bool $forSale = false, ?int $batchId = null): void
    {
        $quantity = round($quantity, 2);
        if ($quantity < 0 || $quantity > ($forSale ? $this->usableQuantity() : (float) $this->current_quantity)) {
            throw ValidationException::withMessages(['quantity' => "Not enough usable {$this->name}. Check stock and expiry dates."]);
        }

        $query = $this->batches()->where('remaining_quantity', '>', 0);
        if ($batchId !== null) {
            $query->whereKey($batchId);
        }
        if ($forSale) {
            $query->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', InventoryBatch::expiryToday()->toDateString()));
        }
        $batches = $query->orderByRaw('expires_at IS NULL')->orderBy('expires_at')->orderBy('id')->lockForUpdate()->get();
        if ($batchId !== null && ($batches->isEmpty() || (float) $batches->first()->remaining_quantity < $quantity)) {
            throw ValidationException::withMessages(['quantity' => 'Quantity exceeds the remaining stock in this batch.']);
        }

        $left = $quantity;
        foreach ($batches as $batch) {
            $used = min($left, (float) $batch->remaining_quantity);
            $batch->update(['remaining_quantity' => round((float) $batch->remaining_quantity - $used, 2)]);
            $left = round($left - $used, 2);
            if ($left <= 0) {
                break;
            }
        }
        $this->decrement('current_quantity', $quantity);
        $this->unsetRelation('batches');
    }

    public function cupSizes(): HasMany
    {
        return $this->hasMany(CupSize::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}

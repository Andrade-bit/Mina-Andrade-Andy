<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_category_id',
        'product_name',
        'image',
    ];

    /**
     * Stock photos matched by a keyword in the product name, relative to
     * public/images. More specific keywords come first.
     *
     * @var array<string, string>
     */
    private const STOCK_PHOTOS = [
        'blue lemonade' => 'products/blue-lemonade.jpg',
        'lemonade' => 'products/lemonade.jpg',
        'americano' => 'products/americano.jpg',
        'cafe latte' => 'products/cafe-latte.jpg',
        'cappuccino' => 'products/cappuccino.jpg',
        'spanish latte' => 'products/spanish-latte.jpg',
        'caramel macchiato' => 'products/caramel-macchiato.jpg',
        'matcha' => 'products/matcha-latte.jpg',
        'mocha' => 'iced-mocha.webp',
        'chocolate' => 'products/chocolate.jpg',
        'strawberry' => 'products/strawberry-milk.jpg',
        'mango' => 'products/mango-juice.jpg',
    ];

    /**
     * A displayable URL for this product's photo: the admin-uploaded image
     * if one was saved, otherwise null (callers fall back to a placeholder).
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * The photo to show for this product: an uploaded image, else a stock
     * photo matched by name, else null when neither exists.
     */
    public function displayImageUrl(): ?string
    {
        if ($this->image) {
            return $this->imageUrl();
        }

        $name = strtolower($this->product_name);

        foreach (self::STOCK_PHOTOS as $keyword => $file) {
            if (str_contains($name, $keyword) && file_exists(public_path('images/'.$file))) {
                return asset('images/'.$file);
            }
        }

        return null;
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    /**
     * The amounts typed for specific cup sizes (Small 1 g, Medium 2 g, Large 3 g).
     */
    public function ingredientSizeAmounts(): HasMany
    {
        return $this->hasMany(ProductIngredientSize::class);
    }

    /**
     * How much of an ingredient one drink in a cup size uses. An amount typed for that size wins; a size left
     * blank uses the ingredient's base amount adjusted for cup volume (see CupSize::recipeScale()). Needs
     * ingredients and ingredientSizeAmounts loaded.
     */
    public function amountFor(Ingredient $ingredient, ?CupSize $size, ?CupSize $recipeSize): float
    {
        $typed = $size
            ? $this->ingredientSizeAmounts->first(fn (ProductIngredientSize $row) => $row->ingredient_id === $ingredient->id && $row->cup_size_id === $size->id)
            : null;

        if ($typed) {
            return (float) $typed->quantity_required;
        }

        return (float) $ingredient->pivot->quantity_required * ($size?->recipeScale($recipeSize) ?? 1.0);
    }

    /**
     * The stock items a drink in this cup size cannot be made without right now: what it needs is more than
     * is on hand. Needs ingredients.inventoryItem and ingredientSizeAmounts loaded; archived stock items are
     * not counted, matching what checkout deducts.
     *
     * @return Collection<int, InventoryItem>
     */
    public function lackingIngredients(?CupSize $size, ?CupSize $recipeSize): Collection
    {
        return $this->ingredients
            ->filter(function (Ingredient $ingredient) use ($size, $recipeSize) {
                $needed = $this->amountFor($ingredient, $size, $recipeSize);

                return $ingredient->inventoryItem
                    && $needed > 0
                    && $needed > (float) $ingredient->inventoryItem->current_quantity;
            })
            ->map(fn (Ingredient $ingredient) => $ingredient->inventoryItem)
            ->unique('id')
            ->values();
    }

    /**
     * Each size this product offers, marked with whether it can be made from the stock on hand and which
     * stock items it lacks when it can't.
     *
     * @param  Collection<int, CupSize>  $cupSizes
     * @return Collection<int, object{cup_size_id: int, size_name: string, price: string, is_available: bool, available: bool, lacking: array<int, string>}>
     */
    public function sizesWithStock(Collection $cupSizes, ?CupSize $recipeSize): Collection
    {
        return $this->effectiveCupSizes($cupSizes)->map(function (object $size) use ($cupSizes, $recipeSize) {
            $lacking = $this->lackingIngredients($cupSizes->firstWhere('id', $size->cup_size_id), $recipeSize);

            $size->available = $lacking->isEmpty();
            $size->lacking = $lacking->pluck('name')->all();

            return $size;
        });
    }

    /**
     * The stock items keeping this product off the POS menu: none while at least one size can still be made,
     * otherwise what the first (cheapest) size lacks.
     *
     * @param  Collection<int, object>  $sizesWithStock  from sizesWithStock()
     * @param  Collection<int, CupSize>  $cupSizes
     * @return Collection<int, InventoryItem>
     */
    public function blockedBy(Collection $sizesWithStock, Collection $cupSizes, ?CupSize $recipeSize): Collection
    {
        if ($sizesWithStock->isEmpty() || $sizesWithStock->contains('available', true)) {
            return collect();
        }

        return $this->lackingIngredients($cupSizes->firstWhere('id', $sizesWithStock->first()->cup_size_id), $recipeSize);
    }

    /**
     * Every active product that cannot be sold right now, keyed by product id, each with the stock items it lacks.
     *
     * @return Collection<int, Collection<int, InventoryItem>>
     */
    public static function unavailableNow(): Collection
    {
        $cupSizes = CupSize::orderBy('price')->get();
        $recipeSize = $cupSizes->firstWhere('is_recipe_size', true);

        return static::with('cupSizePrices', 'ingredients.inventoryItem', 'ingredientSizeAmounts')->get()
            ->mapWithKeys(fn (Product $product) => [$product->id => $product->blockedBy($product->sizesWithStock($cupSizes, $recipeSize), $cupSizes, $recipeSize)])
            ->filter(fn (Collection $lacking) => $lacking->isNotEmpty());
    }

    public function salesTransactionItems(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class);
    }

    public function cupSizePrices(): HasMany
    {
        return $this->hasMany(ProductCupSize::class);
    }

    /**
     * Every cup size this product offers, with its effective price —
     * a per-product override when one exists, otherwise the cup size's
     * own default price. Sizes explicitly turned off are left out.
     *
     * Pass an already-loaded list of cup sizes when calling this for many
     * products, so each call does not query them again.
     *
     * @param  Collection<int, CupSize>|null  $cupSizes
     * @return Collection<int, object{cup_size_id: int, size_name: string, price: string, is_available: bool}>
     */
    public function effectiveCupSizes(?Collection $cupSizes = null): Collection
    {
        $overrides = $this->relationLoaded('cupSizePrices')
            ? $this->cupSizePrices->keyBy('cup_size_id')
            : $this->cupSizePrices()->get()->keyBy('cup_size_id');

        return ($cupSizes ?? CupSize::orderBy('price')->get())->map(function (CupSize $cupSize) use ($overrides) {
            $override = $overrides->get($cupSize->id);

            return (object) [
                'cup_size_id' => $cupSize->id,
                'size_name' => $cupSize->size_name,
                'price' => $override?->price ?? $cupSize->price,
                'is_available' => $override?->is_available ?? true,
            ];
        })->filter(fn ($size) => $size->is_available)->values();
    }
}

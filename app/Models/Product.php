<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'product_name',
        'image',
    ];

    /**
     * A displayable URL for this product's photo: the admin-uploaded image
     * if one was saved, otherwise null (callers fall back to a placeholder).
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot('quantity_required')
            ->withTimestamps();
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
     * @return Collection<int, object{cup_size_id: int, size_name: string, price: string, is_available: bool}>
     */
    public function effectiveCupSizes(): Collection
    {
        $overrides = $this->relationLoaded('cupSizePrices')
            ? $this->cupSizePrices->keyBy('cup_size_id')
            : $this->cupSizePrices()->get()->keyBy('cup_size_id');

        return CupSize::orderBy('price')->get()->map(function (CupSize $cupSize) use ($overrides) {
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

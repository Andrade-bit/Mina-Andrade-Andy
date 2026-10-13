<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use BelongsToShop;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

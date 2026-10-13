<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToShop;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'payment_terms',
    ];

    public function supplyPurchases(): HasMany
    {
        return $this->hasMany(SupplyPurchase::class);
    }
}

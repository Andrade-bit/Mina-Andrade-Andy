<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
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

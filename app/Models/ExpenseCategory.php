<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use BelongsToShop;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = ['name', 'slug', 'status', 'plan_id', 'subscription_status', 'trial_ends_at', 'renews_at'];

    protected function casts(): array
    {
        return ['trial_ends_at' => 'datetime', 'renews_at' => 'datetime'];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function canOperate(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return match ($this->subscription_status) {
            'active' => ! $this->renews_at || $this->renews_at->isFuture(),
            'trial' => $this->trial_ends_at?->isFuture() ?? false,
            default => false,
        };
    }
}

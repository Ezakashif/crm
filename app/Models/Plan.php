<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'max_users',
        'max_leads',
        'max_customers',
        'price_cents',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_users' => 'integer',
            'max_leads' => 'integer',
            'max_customers' => 'integer',
            'price_cents' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function default(): ?self
    {
        return static::query()->where('is_default', true)->first()
            ?? static::query()->active()->orderBy('id')->first();
    }

    public function isUnlimited(string $metric): bool
    {
        return $this->{"max_{$metric}"} === null;
    }
}

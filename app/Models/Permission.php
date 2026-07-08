<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public static function grouped(): \Illuminate\Support\Collection
    {
        return static::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->group ?: 'general');
    }
}

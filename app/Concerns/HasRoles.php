<?php

namespace App\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    protected ?Collection $cachedPermissionSlugs = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string $slug): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('slug', $slug);
        }

        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('slug')->intersect($slugs)->isNotEmpty();
        }

        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function permissionSlugs(): Collection
    {
        if ($this->cachedPermissionSlugs !== null) {
            return $this->cachedPermissionSlugs;
        }

        $this->cachedPermissionSlugs = $this->roles()
            ->with('permissions:id,slug')
            ->get()
            ->flatMap(fn (Role $role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values();

        return $this->cachedPermissionSlugs;
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissionSlugs()->contains($slug);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        return $this->permissionSlugs()->intersect($slugs)->isNotEmpty();
    }

    public function canAssignTasks(): bool
    {
        return $this->hasPermission('assign.tasks');
    }

    /**
     * @param  array<int|string>  $roleIds
     */
    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
        $this->cachedPermissionSlugs = null;
        $this->syncLegacyRoleColumn();
    }

    /**
     * @param  array<int, string>  $slugs
     */
    public function syncRolesBySlug(array $slugs): void
    {
        $roleIds = Role::query()
            ->whereIn('slug', $slugs)
            ->pluck('id')
            ->all();

        $this->syncRoles($roleIds);
    }

    public function syncRolesFromLegacyColumn(): void
    {
        $slug = match ($this->role) {
            'admin' => 'admin',
            'manager' => 'manager',
            default => 'sales',
        };

        $role = Role::query()->where('slug', $slug)->first();

        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    public function syncLegacyRoleColumn(): void
    {
        $slugs = $this->roles()->pluck('slug')->all();

        $this->role = match (true) {
            in_array('admin', $slugs, true) => 'admin',
            in_array('manager', $slugs, true) => 'manager',
            default => 'user',
        };

        $this->saveQuietly();
    }

    public function roleNames(): string
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('name')->join(', ');
        }

        return $this->roles()->pluck('name')->join(', ');
    }
}

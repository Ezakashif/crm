<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistrar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:50',
        ]);

        $permissions = Permission::query()
            ->withCount('roles')
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $term = $filters['search'];

                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when(filled($filters['group'] ?? null), fn ($query) => $query->where('group', $filters['group']))
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $groups = Permission::query()
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('permissions.index', [
            'permissions' => $permissions,
            'groups' => $groups,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Permission::class);

        $groups = Permission::query()
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', 'unique:permissions,slug'],
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $permission = Permission::create($validated);

        $adminRole = Role::query()->where('slug', 'admin')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }

        app(PermissionRegistrar::class)->refreshGates();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        $groups = Permission::query()
            ->whereNotNull('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', Rule::unique('permissions', 'slug')->ignore($permission->id)],
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $permission->update($validated);

        app(PermissionRegistrar::class)->refreshGates();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->roles()->detach();
        $permission->delete();

        app(PermissionRegistrar::class)->refreshGates();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}

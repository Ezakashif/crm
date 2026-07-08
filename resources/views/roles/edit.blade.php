<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Edit Role</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if($role->is_system)
                    <div class="alert alert-info">
                        This is a system role. Its slug cannot be changed, but you can update its permissions.
                    </div>
                @endif

                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input id="slug" name="slug" type="text"
                           class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $role->slug) }}"
                           @if($role->is_system) readonly @endif
                           required>
                    @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $role->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <hr>
                <h5 class="mb-3">Permissions</h5>
                <p class="text-muted">Users with this role will receive the selected permissions.</p>

                <x-permission-checklist
                    :module-permissions="$modulePermissions"
                    :selected="array_map('intval', old('permissions', $role->permissions->pluck('id')->all()))"
                />
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

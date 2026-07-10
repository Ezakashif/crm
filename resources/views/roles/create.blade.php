<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="crm-page-title">Create Role</h1>
            <span class="crm-page-subtitle">Name a role and choose permissions.</span>
        </div>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input id="slug" name="slug" type="text" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug') }}" placeholder="e.g. support_agent" required>
                    <small class="form-text text-muted">Lowercase letters, numbers, dashes and underscores only.</small>
                    @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <hr>
                <h5 class="mb-3">Permissions</h5>
                <p class="text-muted">Select which permissions this role grants to assigned users.</p>

                <x-permission-checklist
                    :module-permissions="$modulePermissions"
                    :selected="array_map('intval', old('permissions', []))"
                />
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

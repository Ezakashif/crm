<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Roles</h1>
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Role
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $errors->first() }}
        </div>
    @endif

    <x-list-filters :reset-url="route('roles.index')">
        <div class="col-md-4 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Name or slug..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
    </x-list-filters>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if($role->description)
                                    <small class="text-muted d-block">{{ $role->description }}</small>
                                @endif
                            </td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @if($role->is_system)
                                    <span class="badge badge-secondary">System</span>
                                @else
                                    <span class="badge badge-light">Custom</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @unless($role->is_system)
                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"
                                                onclick="return confirm('Delete this role?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
            <div class="card-footer clearfix">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

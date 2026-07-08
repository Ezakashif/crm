<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Permissions</h1>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Permission
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

    <x-list-filters :reset-url="route('permissions.index')">
        <div class="col-md-4 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Name or slug..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3 mb-2">
            <label for="group" class="small text-muted mb-1">Group</label>
            <select id="group" name="group" class="form-control form-control-sm">
                <option value="">All groups</option>
                @foreach($groups as $group)
                    <option value="{{ $group }}" @selected(($filters['group'] ?? '') === $group)>{{ ucfirst($group) }}</option>
                @endforeach
            </select>
        </div>
    </x-list-filters>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Group</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>
                                <strong>{{ $permission->name }}</strong>
                                @if($permission->description)
                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                @endif
                            </td>
                            <td><code>{{ $permission->slug }}</code></td>
                            <td>{{ $permission->group ? ucfirst($permission->group) : '—' }}</td>
                            <td>{{ $permission->roles_count }}</td>
                            <td>
                                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('permissions.destroy', $permission) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger"
                                            onclick="return confirm('Delete this permission? It will be removed from all roles.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($permissions->hasPages())
            <div class="card-footer clearfix">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

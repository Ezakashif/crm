<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Users</h1>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add User
                </a>
            @endcan
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

    <x-list-filters :reset-url="route('users.index')">
        <div class="col-md-4 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Name or email..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3 mb-2">
            <label for="role" class="small text-muted mb-1">Role</label>
            <select id="role" name="role" class="form-control form-control-sm">
                <option value="">All roles</option>
                @foreach($roles as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['role'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label for="status" class="small text-muted mb-1">Status</label>
            <select id="status" name="status" class="form-control form-control-sm">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </x-list-filters>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <x-user-avatar :user="$user" :size="36" />
                            </td>
                            <td>
                                @can('view', $user)
                                    <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                                @else
                                    {{ $user->name }}
                                @endcan
                                @if($user->id === auth()->id())
                                    <span class="badge badge-light ml-1">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->roleBadgeClass() }}">
                                    {{ $user->roleNames() }}
                                </span>
                            </td>
                            <td>
                                @can('update', $user)
                                    <form method="POST" action="{{ route('users.status', $user) }}" class="d-inline">
                                        @csrf
                                        <select name="status" class="form-control form-control-sm d-inline-block"
                                                style="width: auto;"
                                                onchange="this.form.submit()"
                                                @if($user->id === auth()->id()) disabled @endif>
                                            @foreach($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected($user->status === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    <span class="badge badge-{{ $user->statusBadgeClass() }}">
                                        {{ $statuses[$user->status] ?? ucfirst($user->status) }}
                                    </span>
                                @endcan
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @can('view', $user)
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-xs btn-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-xs btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                {{ collect($filters ?? [])->filter(fn ($v) => filled($v))->isNotEmpty() ? 'No users match your filters.' : 'No users yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer clearfix">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

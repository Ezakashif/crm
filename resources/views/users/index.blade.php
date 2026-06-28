<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Users</h1>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add User
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
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="badge badge-light ml-1">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->roleBadgeClass() }}">
                                    {{ $roles[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
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
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"
                                                onclick="return confirm('Delete this user?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No users yet.</td>
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

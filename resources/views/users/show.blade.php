<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <x-user-avatar :user="$user" :size="56" class="mr-3" />
                <div>
                    <h1 class="m-0">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="badge badge-light ml-1">You</span>
                        @endif
                    </h1>
                    <small class="text-muted">User profile</small>
                </div>
            </div>
            <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center">
                @can('update', $user)
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-default btn-sm mb-1 mr-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('users.index') }}" class="btn btn-default btn-sm mb-1">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profile Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $user->statusBadgeClass() }}">
                                {{ $statuses[$user->status] ?? ucfirst($user->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Roles</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $user->roleBadgeClass() }}">
                                {{ $user->roleNames() ?: '—' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Joined</dt>
                        <dd class="col-sm-8">{{ $user->created_at?->format('M j, Y g:i A') ?? '—' }}</dd>

                        <dt class="col-sm-4">Last Updated</dt>
                        <dd class="col-sm-8">{{ $user->updated_at?->format('M j, Y g:i A') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @can('delete', $user)
                @if($user->id !== auth()->id())
                    <div class="card">
                        <div class="card-footer text-right">
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete User
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endcan
        </div>
    </div>
</x-app-layout>

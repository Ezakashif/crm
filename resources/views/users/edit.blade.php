<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Edit User</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group text-center mb-4">
                    <x-user-avatar :user="$user" :size="80" class="mb-2" />
                    <div>
                        <label for="photo">Profile Photo</label>
                        <input id="photo" name="photo" type="file" accept="image/*"
                               class="form-control-file @error('photo') is-invalid @enderror">
                        @error('photo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        <small class="form-text text-muted">Optional. Max 2 MB.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Full Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror">
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label>Roles <span class="text-danger">*</span></label>
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input id="role-{{ $role->id }}" name="roles[]" type="checkbox"
                                   class="form-check-input @error('roles') is-invalid @enderror"
                                   value="{{ $role->id }}"
                                   @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->all()), true))
                                   @if($user->id === auth()->id()) disabled @endif>
                            <label class="form-check-label" for="role-{{ $role->id }}">
                                {{ $role->name }}
                                @if($role->description)
                                    <small class="text-muted d-block">{{ $role->description }}</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                    @if($user->id === auth()->id())
                        @foreach($user->roles as $role)
                            <input type="hidden" name="roles[]" value="{{ $role->id }}">
                        @endforeach
                        <small class="form-text text-muted">You cannot change your own roles.</small>
                    @endif
                    @error('roles')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required
                            @if($user->id === auth()->id()) disabled @endif>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $user->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="status" value="active">
                        <small class="form-text text-muted">You cannot change your own status.</small>
                    @endif
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

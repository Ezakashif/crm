<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Create User</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Full Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="photo">Profile Photo</label>
                    <input id="photo" name="photo" type="file" accept="image/*"
                           class="form-control-file @error('photo') is-invalid @enderror">
                    @error('photo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    <small class="form-text text-muted">Optional. JPEG, PNG, GIF or WebP. Max 2 MB.</small>
                </div>

                <div class="form-group">
                    <label for="role">Role <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', 'user') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

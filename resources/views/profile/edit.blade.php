<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Profile</h1>
    </x-slot>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profile Photo</h3>
                </div>
                <div class="card-body text-center">
                    <x-user-avatar :user="$user" :size="120" class="mb-3" />
                    <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="form-group text-left">
                            <label for="photo">Upload new photo</label>
                            <input id="photo" name="photo" type="file" accept="image/*"
                                   class="form-control-file @error('photo') is-invalid @enderror">
                            @error('photo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                            <small class="form-text text-muted">JPEG, PNG, GIF or WebP. Max 2 MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload"></i> Upload Photo
                        </button>
                    </form>
                    @if($user->photo_path)
                        <form method="post" action="{{ route('profile.photo.destroy') }}" class="mt-2">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Remove your profile photo?')">
                                <i class="fas fa-trash"></i> Remove Photo
                            </button>
                        </form>
                    @endif
                    @if (session('status') === 'photo-updated')
                        <p class="text-success mt-2 mb-0">Photo updated.</p>
                    @endif
                    @if (session('status') === 'photo-removed')
                        <p class="text-success mt-2 mb-0">Photo removed.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="alert alert-warning mb-0">
                                Your email is unverified.
                            </div>
                            @if (session('status') === 'verification-link-sent')
                                <div class="alert alert-success mt-2 mb-0">A new verification link has been sent.</div>
                            @endif
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        @if (session('status') === 'profile-updated')
                            <span class="text-success ml-2">Saved.</span>
                        @endif
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <button form="send-verification" type="submit" class="btn btn-link">Resend verification email</button>
                        @endif
                    </div>
                </form>
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                @endif
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Password</h3>
                </div>
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="update_password_current_password">Current Password</label>
                            <input id="update_password_current_password" name="current_password" type="password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                            @error('current_password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="update_password_password">New Password</label>
                            <input id="update_password_password" name="password" type="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                            @error('password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="update_password_password_confirmation">Confirm Password</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update Password</button>
                        @if (session('status') === 'password-updated')
                            <span class="text-success ml-2">Saved.</span>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Delete Account</h3>
                </div>
                <div class="card-body">
                    <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Please enter your password to confirm you would like to permanently delete your account.</p>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                        @error('password', 'userDeletion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    $('#deleteAccountModal').modal('show');
                });
            </script>
        @endpush
    @endif
</x-app-layout>

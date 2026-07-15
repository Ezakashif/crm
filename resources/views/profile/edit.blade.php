<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Profile"
            subtitle="Your account details and security settings."
            :breadcrumbs="[
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Profile'],
            ]"
        />
    </x-slot>

    @if ($errors->any() && $errors->default->isNotEmpty())
        <div class="alert alert-danger alert-dismissible crm-keep-alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Dismiss">&times;</button>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card card-outline card-primary">
                <div class="card-body text-center">
                    <x-form-section title="Profile photo" description="Shown across the CRM next to your name.">
                        <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="text-left">
                            @csrf
                            @method('patch')
                            <x-image-crop-upload
                                name="photo"
                                id="photo"
                                label="Upload new photo"
                                :preview-url="$user->photo_path ? $user->photoUrl() : null"
                                :required="true"
                                help="Choose a photo, then drag it to adjust inside the frame. JPEG, PNG, GIF or WebP. Max 2 MB."
                            />
                            <button type="submit" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-upload" aria-hidden="true"></i> Upload photo
                            </button>
                        </form>
                        @if ($user->photo_path)
                            <form method="post" action="{{ route('profile.photo.destroy') }}" class="mt-2">
                                @csrf
                                @method('delete')
                                <button
                                    type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                    data-crm-confirm="Remove your profile photo? Your initials will be shown instead."
                                    data-crm-confirm-title="Remove photo"
                                    data-crm-confirm-label="Remove"
                                >
                                    <i class="fas fa-trash" aria-hidden="true"></i> Remove photo
                                </button>
                            </form>
                        @endif
                    </x-form-section>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-outline card-primary mb-3">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    <div class="card-body">
                        <x-form-section
                            title="Profile information"
                            description="Update your name and email address."
                        >
                            <div class="form-group">
                                <x-form-label for="name" :required="true">Name</x-form-label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required autocomplete="name">
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group mb-0">
                                <x-form-label for="email" :required="true">Email</x-form-label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="alert alert-warning crm-keep-alert mt-3 mb-0">
                                    Your email is unverified.
                                    <button form="send-verification" type="submit" class="btn btn-link btn-sm p-0 align-baseline">
                                        Resend verification email
                                    </button>
                                </div>
                            @endif
                        </x-form-section>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                @endif
            </div>

            <div class="card card-outline card-secondary mb-3">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <div class="card-body">
                        <x-form-section
                            title="Update password"
                            description="Use a long, unique password to keep your account secure."
                        >
                            <div class="form-group">
                                <x-form-label for="update_password_current_password" :required="true">Current password</x-form-label>
                                <input id="update_password_current_password" name="current_password" type="password"
                                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="current-password" required>
                                @error('current_password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <x-form-label for="update_password_password" :required="true">New password</x-form-label>
                                <input id="update_password_password" name="password" type="password"
                                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                       autocomplete="new-password" required>
                                @error('password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group mb-0">
                                <x-form-label for="update_password_password_confirmation" :required="true">Confirm password</x-form-label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                       class="form-control" autocomplete="new-password" required>
                            </div>
                        </x-form-section>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update password</button>
                    </div>
                </form>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-body">
                    <x-form-section
                        title="Delete account"
                        description="Once your account is deleted, all of its resources and data will be permanently deleted."
                    >
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
                            Delete account
                        </button>
                    </x-form-section>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalTitle">Delete account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Enter your password to confirm you want to permanently delete your account.</p>
                    <div class="form-group mb-0">
                        <x-form-label for="password" :required="true">Password</x-form-label>
                        <input id="password" name="password" type="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               autocomplete="current-password" required>
                        @error('password', 'userDeletion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete account</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    $('#deleteAccountModal').modal('show');
                });
            </script>
        @endpush
    @endif
</x-app-layout>

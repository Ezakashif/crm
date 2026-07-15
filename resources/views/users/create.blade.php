<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Create user"
            subtitle="Invite a teammate and set their role."
            :breadcrumbs="[
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Users', 'url' => route('users.index')],
                ['label' => 'Create'],
            ]"
        />
    </x-slot>

    <div class="card card-outline card-primary">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <x-form-section title="Profile" description="Basic account details.">
                    <div class="form-group">
                        <x-form-label for="name" :required="true">Full name</x-form-label>
                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required autocomplete="name">
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <x-form-label for="email" :required="true">Email</x-form-label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autocomplete="email">
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-0">
                        <x-image-crop-upload
                            name="photo"
                            id="photo"
                            label="Profile photo"
                            help="Optional. Choose a photo, then drag it to adjust inside the frame. JPEG, PNG, GIF or WebP. Max 2 MB."
                        />
                    </div>
                </x-form-section>

                <x-form-section title="Security" description="Set an initial password.">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-form-label for="password" :required="true">Password</x-form-label>
                                <input id="password" name="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required autocomplete="new-password">
                                @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <x-form-label for="password_confirmation" :required="true">Confirm password</x-form-label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       class="form-control" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </x-form-section>

                <x-form-section title="Access" description="Roles and account status.">
                    <div class="form-group">
                        <x-form-label :required="true">Roles</x-form-label>
                        @php
                            $defaultRoleIds = old('roles', [$roles->firstWhere('slug', 'sales')?->id]);
                        @endphp
                        @foreach ($roles as $role)
                            <div class="form-check">
                                <input id="role-{{ $role->id }}" name="roles[]" type="checkbox"
                                       class="form-check-input @error('roles') is-invalid @enderror"
                                       value="{{ $role->id }}"
                                       @checked(in_array($role->id, array_filter($defaultRoleIds), true))>
                                <label class="form-check-label" for="role-{{ $role->id }}">
                                    {{ $role->name }}
                                    @if ($role->description)
                                        <small class="text-muted d-block">{{ $role->description }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                        @error('roles')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-0">
                        <x-form-label for="status" :required="true">Status</x-form-label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </x-form-section>
            </div>

            <div class="card-footer">
                <div class="crm-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" aria-hidden="true"></i> Save user
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

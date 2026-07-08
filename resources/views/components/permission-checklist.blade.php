@props([
    'permissionGroups',
    'selected' => [],
    'name' => 'permissions',
])

<div class="permission-checklist">
    @foreach($permissionGroups as $group => $permissions)
        <div class="mb-3">
            <h6 class="text-muted text-uppercase mb-2">{{ ucfirst($group) }}</h6>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="form-check">
                            <input id="{{ $name }}-{{ $permission->id }}"
                                   name="{{ $name }}[]"
                                   type="checkbox"
                                   class="form-check-input @error($name) is-invalid @enderror"
                                   value="{{ $permission->id }}"
                                   @checked(in_array($permission->id, $selected, true))>
                            <label class="form-check-label" for="{{ $name }}-{{ $permission->id }}">
                                {{ $permission->name }}
                                <small class="text-muted d-block">{{ $permission->slug }}</small>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    @error($name)<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
</div>

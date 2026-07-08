<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Edit Permission</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('permissions.update', $permission) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $permission->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug <span class="text-danger">*</span></label>
                    <input id="slug" name="slug" type="text" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $permission->slug) }}" required>
                    @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="group">Group</label>
                    <input id="group" name="group" type="text" class="form-control @error('group') is-invalid @enderror"
                           value="{{ old('group', $permission->group) }}" list="permission-groups">
                    <datalist id="permission-groups">
                        @foreach($groups as $group)
                            <option value="{{ $group }}">
                        @endforeach
                    </datalist>
                    @error('group')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $permission->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Permission
                </button>
                <a href="{{ route('permissions.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Create Task</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Task Title <span class="text-danger">*</span></label>
                    <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required>
                    @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                        <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input id="due_date" name="due_date" type="date" class="form-control" value="{{ old('due_date') }}">
                </div>

                <div class="form-group">
                    <label for="assigned_to">Assign To <span class="text-danger">*</span></label>
                    <select id="assigned_to" name="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" required>
                        <option value="">— Select user —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Task
                </button>
                <a href="{{ route('tasks.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Edit Task</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Task Title</label>
                    <input id="title" name="title" type="text" class="form-control" value="{{ old('title', $task->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending" @selected(old('status', $task->status) === 'pending')>Pending</option>
                        <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>In Progress</option>
                        <option value="completed" @selected(old('status', $task->status) === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status', $task->status) === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
                        <option value="urgent" @selected(old('priority', $task->priority) === 'urgent')>Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input id="due_date" name="due_date" type="date" class="form-control"
                           value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                </div>

                @can('assign', $task)
                    <div class="form-group">
                        <label for="assigned_to">Assign To <span class="text-danger">*</span></label>
                        <select id="assigned_to" name="assigned_to" class="form-control" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('assigned_to', $task->assigned_to) == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="form-group">
                        <label>Assigned To</label>
                        <input type="text" class="form-control" value="{{ $task->assignee?->name ?? '—' }}" disabled>
                    </div>
                @endif
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    @can('update', $task)
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Task
                        </button>
                    @endcan
                    <a href="{{ route('tasks.index') }}" class="btn btn-default">Cancel</a>
                </div>
                @can('delete', $task)
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline"
                          onsubmit="return confirm('Delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endcan
            </div>
        </form>
    </div>
</x-app-layout>

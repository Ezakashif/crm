<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Tasks</h1>
            @can('create', App\Models\Task::class)
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Task
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $statusColors = [
            'pending' => 'card-warning',
            'in_progress' => 'card-info',
            'completed' => 'card-success',
            'cancelled' => 'card-secondary',
        ];
        $canDragTasks = auth()->user()->hasPermission('update.tasks')
            || auth()->user()->hasPermission('change_status.tasks');
    @endphp

    <x-list-filters :reset-url="route('tasks.index')">
        <div class="col-md-3 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Title or description..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-2 mb-2">
            <label for="status" class="small text-muted mb-1">Status</label>
            <select id="status" name="status" class="form-control form-control-sm">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <label for="priority" class="small text-muted mb-1">Priority</label>
            <select id="priority" name="priority" class="form-control form-control-sm">
                <option value="">All priorities</option>
                @foreach($priorities as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @if(auth()->user()->canViewAllTasks())
            <div class="col-md-3 mb-2">
                <label for="assigned_to" class="small text-muted mb-1">Assigned To</label>
                <select id="assigned_to" name="assigned_to" class="form-control form-control-sm">
                    <option value="">Anyone</option>
                    <option value="unassigned" @selected(($filters['assigned_to'] ?? '') === 'unassigned')>Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(($filters['assigned_to'] ?? '') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </x-list-filters>

    @if($tasks->isEmpty())
        <div class="alert alert-info">
            {{ collect($filters ?? [])->filter(fn ($v) => filled($v))->isNotEmpty() ? 'No tasks match your filters.' : 'No tasks yet.' }}
        </div>
    @endif

    <div class="row">
        @foreach($statuses as $statusKey => $statusTitle)
            <div class="col-lg-3 col-md-6">
                <div class="card {{ $statusColors[$statusKey] ?? 'card-secondary' }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ $statusTitle }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-light column-count">
                                {{ $tasks->where('status', $statusKey)->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body task-column p-2" data-status="{{ $statusKey }}" style="min-height: 350px;">
                        @foreach($tasks->where('status', $statusKey) as $task)
                            @php($canChangeStatus = auth()->user()->can('changeStatus', $task))
                            <div class="card card-sm mb-2 task-card"
                                 data-task-id="{{ $task->id }}"
                                 data-draggable="{{ $canChangeStatus ? '1' : '0' }}"
                                 style="cursor: {{ $canChangeStatus ? 'grab' : 'default' }};">
                                <div class="card-body p-2">
                                    <h6 class="mb-1">{{ $task->title }}</h6>
                                    @if($task->description)
                                        <p class="text-muted small mb-2">{{ Str::limit($task->description, 80) }}</p>
                                    @endif
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><i class="fas fa-user"></i> {{ optional($task->assignee)->name ?? 'Unassigned' }}</span>
                                        <span><i class="fas fa-calendar"></i> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M') : '—' }}</span>
                                    </div>
                                    <div class="mt-2">
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-xs btn-default">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $task)
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger"
                                                        onclick="return confirm('Delete this task?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        @if($canDragTasks)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || @json(csrf_token());

                    function refreshColumnCounts() {
                        document.querySelectorAll('.task-column').forEach(column => {
                            const badge = column.closest('.card')?.querySelector('.column-count');
                            if (badge) {
                                badge.textContent = column.querySelectorAll('.task-card').length;
                            }
                        });
                    }

                    document.querySelectorAll('.task-column').forEach(column => {
                        new Sortable(column, {
                            group: 'tasks-kanban',
                            animation: 200,
                            draggable: '.task-card[data-draggable="1"]',
                            ghostClass: 'opacity-50',
                            onEnd: function (evt) {
                                const taskId = evt.item.dataset.taskId;
                                const status = evt.to.dataset.status;
                                const sortOrder = evt.newIndex + 1;

                                if (evt.from === evt.to && evt.oldIndex === evt.newIndex) {
                                    return;
                                }

                                fetch(@json(route('tasks.board.update')), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        task_id: taskId,
                                        status: status,
                                        sort_order: sortOrder
                                    })
                                }).then(async (res) => {
                                    let data = null;
                                    try {
                                        data = await res.json();
                                    } catch (e) {
                                        data = null;
                                    }

                                    if (! res.ok || ! data?.success) {
                                        evt.from.insertBefore(
                                            evt.item,
                                            evt.from.children[evt.oldIndex] || null
                                        );
                                        alert(data?.message || 'Unable to update task status.');
                                        return;
                                    }

                                    refreshColumnCounts();
                                }).catch(() => {
                                    evt.from.insertBefore(
                                        evt.item,
                                        evt.from.children[evt.oldIndex] || null
                                    );
                                    alert('Something went wrong while updating the task.');
                                });
                            }
                        });
                    });
                });
            </script>
        @endif
    @endpush
</x-app-layout>

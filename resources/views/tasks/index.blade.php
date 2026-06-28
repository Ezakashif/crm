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
    @endphp

    <div class="row">
        @foreach($statuses as $statusKey => $statusTitle)
            <div class="col-lg-3 col-md-6">
                <div class="card {{ $statusColors[$statusKey] ?? 'card-secondary' }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ $statusTitle }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-light">
                                {{ $tasks->where('status', $statusKey)->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body task-column p-2" data-status="{{ $statusKey }}" style="min-height: 350px;">
                        @foreach($tasks->where('status', $statusKey) as $task)
                            <div class="card card-sm mb-2 task-card" data-task-id="{{ $task->id }}" style="cursor: move;">
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
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-xs btn-default">Edit</a>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.task-column').forEach(column => {
                    new Sortable(column, {
                        group: 'kanban',
                        animation: 200,
                        draggable: '.task-card',
                        ghostClass: 'opacity-50',
                        onEnd: function (evt) {
                            fetch("{{ route('tasks.board.update') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "Accept": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    task_id: evt.item.dataset.taskId,
                                    status: evt.to.dataset.status,
                                    sort_order: evt.newIndex + 1
                                })
                            }).then(res => res.json()).then(data => {
                                if (!data.success) alert('Unable to update task.');
                            }).catch(() => alert('Something went wrong.'));
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>

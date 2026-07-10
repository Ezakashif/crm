<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="m-0">{{ $task->title }}</h1>
                <small class="text-muted">Task details</small>
            </div>
            <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center">
                @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-default btn-sm mb-1 mr-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('tasks.index') }}" class="btn btn-default btn-sm mb-1">
                    <i class="fas fa-arrow-left"></i> Back to Board
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusBadge = match ($task->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary',
        };

        $priorityBadge = match ($task->priority) {
            'low' => 'secondary',
            'medium' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Task Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $statusBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Priority</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $priorityBadge }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Assigned To</dt>
                        <dd class="col-sm-8">{{ $task->assignee?->name ?? 'Unassigned' }}</dd>

                        <dt class="col-sm-4">Created By</dt>
                        <dd class="col-sm-8">{{ $task->creator?->name ?? '—' }}</dd>

                        <dt class="col-sm-4">Due Date</dt>
                        <dd class="col-sm-8">
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Completed At</dt>
                        <dd class="col-sm-8">
                            @if($task->completed_at)
                                {{ \Carbon\Carbon::parse($task->completed_at)->format('M j, Y g:i A') }}
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Related Lead</dt>
                        <dd class="col-sm-8">
                            @if($task->lead)
                                @can('view', $task->lead)
                                    <a href="{{ route('leads.show', $task->lead) }}">{{ $task->lead->name }}</a>
                                @else
                                    {{ $task->lead->name }}
                                @endcan
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Related Customer</dt>
                        <dd class="col-sm-8">
                            @if($task->customer)
                                @can('view', $task->customer)
                                    <a href="{{ route('customers.show', $task->customer) }}">{{ $task->customer->name }}</a>
                                @else
                                    {{ $task->customer->name }}
                                @endcan
                            @else
                                —
                            @endif
                        </dd>
                    </dl>

                    <hr>
                    <p class="text-muted small mb-1">Description</p>
                    @if(filled($task->description))
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $task->description }}</p>
                    @else
                        <p class="mb-0 text-muted">No description.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @can('changeStatus', $task)
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Update Status</h3>
                    </div>
                    <form method="POST" action="{{ route('tasks.status', $task) }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="status" class="small text-muted">Status</label>
                                <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="pending" @selected($task->status === 'pending')>Pending</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>In Progress</option>
                                    <option value="completed" @selected($task->status === 'completed')>Completed</option>
                                    <option value="cancelled" @selected($task->status === 'cancelled')>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-1">Created</p>
                    <p class="mb-3">{{ $task->created_at?->format('M j, Y g:i A') ?? '—' }}</p>
                    <p class="text-muted small mb-1">Last Updated</p>
                    <p class="mb-0">{{ $task->updated_at?->format('M j, Y g:i A') ?? '—' }}</p>
                </div>
                @can('delete', $task)
                    <div class="card-footer text-right">
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                              onsubmit="return confirm('Delete this task?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete Task
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>

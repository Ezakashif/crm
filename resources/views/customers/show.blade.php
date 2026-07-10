<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="m-0">{{ $customer->name }}</h1>
                <small class="text-muted">{{ $customer->company_name ?? 'Customer profile' }}</small>
            </div>
            <div class="mt-2 mt-md-0 d-flex flex-wrap align-items-center">
                @can('update', $customer)
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-default btn-sm mb-1 mr-1">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('customers.index') }}" class="btn btn-default btn-sm mb-1">
                    <i class="fas fa-arrow-left"></i> Back to Customers
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

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Customer Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8">
                            @if($customer->phone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $customer->phone) }}">{{ $customer->phone }}</a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Company</dt>
                        <dd class="col-sm-8">{{ $customer->company_name ?? '—' }}</dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $customer->address ?: '—' }}</dd>

                        <dt class="col-sm-4">Created By</dt>
                        <dd class="col-sm-8">{{ $customer->creator?->name ?? '—' }}</dd>

                        <dt class="col-sm-4">Created</dt>
                        <dd class="col-sm-8">{{ $customer->created_at?->format('M j, Y g:i A') ?? '—' }}</dd>
                    </dl>

                    <hr>
                    <p class="text-muted small mb-1">Notes</p>
                    @if(filled($customer->notes))
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $customer->notes }}</p>
                    @else
                        <p class="mb-0 text-muted">No notes.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Related Tasks</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($tasks as $task)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @can('view', $task)
                                        <a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
                                    @else
                                        {{ $task->title }}
                                    @endcan
                                    <div class="small text-muted">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        · {{ $task->assignee?->name ?? 'Unassigned' }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No related tasks.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            @can('delete', $customer)
                <div class="card">
                    <div class="card-footer text-right">
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                              onsubmit="return confirm('Delete this customer?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete Customer
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>

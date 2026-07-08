<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Customers</h1>
            @can('create', App\Models\Customer::class)
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Customer
                </a>
            @endcan
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <x-list-filters :reset-url="route('customers.index')">
        <div class="col-md-4 mb-2">
            <label for="search" class="small text-muted mb-1">Search</label>
            <input id="search" name="search" type="text" class="form-control form-control-sm"
                   placeholder="Name, email, phone, company..."
                   value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3 mb-2">
            <label for="status" class="small text-muted mb-1">Status</label>
            <select id="status" name="status" class="form-control form-control-sm">
                <option value="">All statuses</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
    </x-list-filters>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Status</th>
                        @canany(['update.customers', 'delete.customers'])
                            <th>Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email ?? '—' }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td>{{ $customer->company_name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            @canany(['update', 'delete'], $customer)
                                <td>
                                    @can('update', $customer)
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endcan
                                    @can('delete', $customer)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Delete this customer?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                {{ collect($filters ?? [])->filter(fn ($v) => filled($v))->isNotEmpty() ? 'No customers match your filters.' : 'No customers yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="card-footer clearfix">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

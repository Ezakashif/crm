<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0">Customers</h1>
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

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
                        <th>Actions</th>
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
                            <td>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger"
                                            onclick="return confirm('Delete this customer?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No customers yet.</td>
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

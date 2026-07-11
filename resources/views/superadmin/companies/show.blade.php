@extends('superadmin.layout')

@section('title', $company->name)
@section('heading', $company->name)
@section('subheading', $company->slug)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="sa-card">
            <div class="sa-muted mb-1">Status</div>
            <div class="mb-3">
                <span class="badge badge-{{ $company->status === 'active' ? 'active' : 'suspended' }}">
                    {{ $statuses[$company->status] ?? ucfirst($company->status) }}
                </span>
            </div>

            <div class="sa-muted small mb-3">Created {{ $company->created_at?->toDayDateTimeString() }}</div>

            <div class="d-flex flex-wrap">
                <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-sm btn-outline-light mr-2 mb-2">Edit</a>

                @if ($company->status === 'active')
                    <form method="POST" action="{{ route('superadmin.companies.status', $company) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="suspended">
                        <button class="btn btn-sm btn-danger mb-2" onclick="return confirm('Suspend this company? Users will be blocked from the CRM.')">
                            Suspend
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('superadmin.companies.status', $company) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-sm btn-success mb-2">Activate</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row">
            @foreach ([
                'users_count' => 'Users',
                'leads_count' => 'Leads',
                'customers_count' => 'Customers',
                'tasks_count' => 'Tasks',
                'roles_count' => 'Roles',
            ] as $key => $label)
                <div class="col-sm-4">
                    <div class="sa-card">
                        <div class="sa-muted mb-1">{{ $label }}</div>
                        <div class="sa-stat">{{ number_format($company->{$key}) }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sa-card">
            <h2 class="h6 text-white mb-3">Company admins</h2>
            @if ($admins->isEmpty())
                <p class="sa-muted mb-0">No admin users assigned yet.</p>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach ($admins as $admin)
                        <li class="mb-2">
                            <div class="text-white">{{ $admin->name }}</div>
                            <div class="sa-muted small">{{ $admin->email }} · {{ $admin->status }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

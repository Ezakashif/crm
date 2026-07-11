@extends('superadmin.layout')

@section('title', 'Super Admins')
@section('heading', 'Super Admins')
@section('subheading', 'Platform operators with full access')

@section('content')
<div class="sa-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="sa-muted">{{ $users->total() }} platform administrators</div>
        <a href="{{ route('superadmin.super-admins.create') }}" class="btn btn-sm btn-info">Create Super Admin</a>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Last login</th>
                <th>Created</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="text-white">{{ $user->name }}</td>
                    <td class="sa-muted">{{ $user->email }}</td>
                    <td>{{ $user->status }}</td>
                    <td class="sa-muted">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="sa-muted">{{ $user->created_at?->format('Y-m-d') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection

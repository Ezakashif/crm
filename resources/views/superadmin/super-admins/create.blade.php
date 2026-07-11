@extends('superadmin.layout')

@section('title', 'Create Super Admin')
@section('heading', 'Create Super Admin')
@section('subheading', 'Add another platform operator')

@section('content')
<div class="sa-card" style="max-width: 560px;">
    <form method="POST" action="{{ route('superadmin.super-admins.store') }}">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="d-flex">
            <button class="btn btn-info mr-2">Create</button>
            <a href="{{ route('superadmin.super-admins.index') }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>
@endsection

@extends('superadmin.layout')

@section('title', 'Edit '.$company->name)
@section('heading', 'Edit company')
@section('subheading', $company->slug)

@section('content')
<div class="sa-card" style="max-width: 640px;">
    <form method="POST" action="{{ route('superadmin.companies.update', $company) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Company name</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $company->slug) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="custom-select" required>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $company->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex">
            <button class="btn btn-info mr-2">Save changes</button>
            <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>
@endsection

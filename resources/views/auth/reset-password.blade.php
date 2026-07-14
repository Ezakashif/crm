@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('Reset Password'))

@section('auth_body')
    <form action="{{ route('password.store') }}" method="post">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="input-group mb-3">
            <input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                   value="{{ old('company', $request->query('company')) }}" placeholder="{{ __('Workspace slug') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-building"></span></div>
            </div>
            @error('company')
                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $request->email) }}" placeholder="{{ __('Email') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('Password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="{{ __('Confirm Password') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-block btn-primary">
            <span class="fas fa-sync-alt"></span> {{ __('Reset Password') }}
        </button>
    </form>
@stop

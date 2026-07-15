@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('Reset Password'))

@section('auth_body')
    <p class="crm-auth-lead">{{ __('Choose a new password for your workspace account.') }}</p>

    <form action="{{ route('password.store') }}" method="post" class="crm-auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="company">{{ __('Workspace slug') }}</label>
            <div class="input-group">
                <input id="company" type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                       value="{{ old('company', $request->query('company')) }}" placeholder="{{ __('Workspace slug') }}" required autofocus>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-building" aria-hidden="true"></span></div>
                </div>
                @error('company')
                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="email">{{ __('Email') }}</label>
            <div class="input-group">
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}" placeholder="{{ __('Email') }}" required autocomplete="username">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-envelope" aria-hidden="true"></span></div>
                </div>
                @error('email')
                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('Password') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock" aria-hidden="true"></span></div>
                </div>
                @error('password')
                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="input-group">
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                       placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock" aria-hidden="true"></span></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
            <span class="fas fa-sync-alt" aria-hidden="true"></span> {{ __('Reset Password') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="crm-auth-footer-link mb-0">
        <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
    </p>
@stop

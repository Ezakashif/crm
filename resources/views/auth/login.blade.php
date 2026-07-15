@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php
    $loginUrl = route('login');
    $passResetUrl = route('password.request');
@endphp

@section('auth_header', __('Sign in to your workspace'))

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-info crm-keep-alert">
            {{ session('status') }}
        </div>
    @endif

    <p class="crm-auth-lead">{{ __('Enter your workspace and credentials to continue.') }}</p>

    <form action="{{ $loginUrl }}" method="post" class="crm-auth-form">
        @csrf

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="company">{{ __('Workspace slug') }}</label>
            <div class="input-group">
                <input id="company" type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                       value="{{ old('company') }}" placeholder="{{ __('Leave blank for platform admin') }}"
                       autocomplete="organization" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-building {{ config('adminlte.classes_auth_icon', '') }}" aria-hidden="true"></span>
                    </div>
                </div>
                @error('company')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="email">{{ __('Email') }}</label>
            <div class="input-group">
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autocomplete="username" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}" aria-hidden="true"></span>
                    </div>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="crm-auth-label" for="password">{{ __('Password') }}</label>
            <div class="input-group">
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('adminlte::adminlte.password') }}" autocomplete="current-password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}" aria-hidden="true"></span>
                    </div>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row align-items-center mb-3">
            <div class="col-7">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
                    <span class="fas fa-sign-in-alt" aria-hidden="true"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    @if ($passResetUrl)
        <p class="crm-auth-footer-link mb-1">
            <a href="{{ $passResetUrl }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    @if (! empty($registrationEnabled))
        <p class="crm-auth-footer-link mb-0">
            <a href="{{ route('register') }}">{{ __('Register a new workspace') }}</a>
        </p>
    @endif
@stop

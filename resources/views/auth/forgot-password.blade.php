@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('Forgot your password?'))

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-info crm-keep-alert">{{ session('status') }}</div>
    @endif

    <p class="login-box-msg">{{ __('Enter your email to receive a reset link.') }}</p>

    <form action="{{ route('password.email') }}" method="post" class="crm-auth-form">
        @csrf

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
            {{ __('Email Password Reset Link') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="crm-auth-footer-link mb-0">
        <a href="{{ route('login') }}">{{ __('Back to login') }}</a>
    </p>
@stop

@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('Verify Your Email Address'))

@section('auth_body')
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success crm-keep-alert">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <p class="crm-auth-lead">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('Resend Verification Email') }}
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-default btn-block">
            {{ __('Log Out') }}
        </button>
    </form>
@stop

@extends('adminlte::auth.login')

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-info">
            {{ session('status') }}
        </div>
    @endif

    @parent
@endsection

@section('auth_footer')
    @parent
    @if (! empty($registrationEnabled))
        <p class="my-0">
            <a href="{{ route('register') }}">{{ __('Register a new workspace') }}</a>
        </p>
    @endif
@endsection

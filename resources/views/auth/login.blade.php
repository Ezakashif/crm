@extends('adminlte::auth.login')

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-info">
            {{ session('status') }}
        </div>
    @endif

    @parent
@endsection

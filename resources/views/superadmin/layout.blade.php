<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --sa-bg: #0f172a;
            --sa-panel: #111827;
            --sa-border: #1f2937;
            --sa-accent: #38bdf8;
            --sa-text: #e5e7eb;
            --sa-muted: #94a3b8;
        }
        body {
            background: radial-gradient(circle at top left, #1e293b, #020617 55%);
            color: var(--sa-text);
            min-height: 100vh;
        }
        .sa-shell { display: flex; min-height: 100vh; }
        .sa-nav {
            width: 240px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--sa-border);
            padding: 1.5rem 1rem;
        }
        .sa-brand {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .sa-brand span { color: var(--sa-accent); }
        .sa-nav a {
            display: block;
            color: var(--sa-muted);
            padding: 0.6rem 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            text-decoration: none;
        }
        .sa-nav a:hover, .sa-nav a.active {
            background: rgba(56, 189, 248, 0.12);
            color: #fff;
        }
        .sa-main { flex: 1; padding: 1.75rem; }
        .sa-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .sa-card {
            background: rgba(17, 24, 39, 0.9);
            border: 1px solid var(--sa-border);
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .sa-stat { font-size: 1.75rem; font-weight: 700; color: #fff; }
        .sa-muted { color: var(--sa-muted); }
        .table { color: var(--sa-text); }
        .table thead th { border-top: 0; border-color: var(--sa-border); color: var(--sa-muted); }
        .table td, .table th { border-color: var(--sa-border); vertical-align: middle; }
        .form-control, .custom-select {
            background: #0b1220;
            border-color: var(--sa-border);
            color: #fff;
        }
        .form-control:focus, .custom-select:focus {
            background: #0b1220;
            color: #fff;
            border-color: var(--sa-accent);
            box-shadow: none;
        }
        .badge-active { background: #065f46; }
        .badge-suspended { background: #7f1d1d; }
        @media (max-width: 768px) {
            .sa-shell { flex-direction: column; }
            .sa-nav { width: 100%; border-right: 0; border-bottom: 1px solid var(--sa-border); }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="sa-shell">
    <aside class="sa-nav">
        <div class="sa-brand">{{ config('app.name') }} <span>Platform</span></div>
        <a href="{{ route('superadmin.dashboard') }}" class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
        <a href="{{ route('superadmin.companies.index') }}" class="{{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}">
            <i class="fas fa-building mr-2"></i> Companies
        </a>
        <hr style="border-color: var(--sa-border);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-muted">
                <i class="fas fa-sign-out-alt mr-2"></i> Log out
            </button>
        </form>
    </aside>

    <main class="sa-main">
        <div class="sa-top">
            <div>
                <h1 class="h3 mb-0 text-white">@yield('heading', 'Super Admin')</h1>
                @hasSection('subheading')
                    <div class="sa-muted mt-1">@yield('subheading')</div>
                @endif
            </div>
            <div class="sa-muted">
                {{ auth()->user()->name }}
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>

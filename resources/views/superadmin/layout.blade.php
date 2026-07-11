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
            --sa-ok: #10b981;
            --sa-warn: #f59e0b;
            --sa-danger: #ef4444;
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
            flex-shrink: 0;
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
        .sa-main { flex: 1; padding: 1.75rem; min-width: 0; }
        .sa-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
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
        .table thead th { border-top: 0; border-color: var(--sa-border); color: var(--sa-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em; }
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
        .badge-active, .badge-ok { background: #065f46; }
        .badge-suspended, .badge-danger { background: #7f1d1d; }
        .badge-trial, .badge-warning { background: #92400e; }
        .badge-expired { background: #4b5563; }
        .sa-health-ok { color: var(--sa-ok); }
        .sa-health-warning { color: var(--sa-warn); }
        .sa-health-error { color: var(--sa-danger); }
        .sa-health-unknown { color: var(--sa-muted); }
        .sa-search { position: relative; min-width: 240px; }
        .sa-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 40;
            background: #0b1220;
            border: 1px solid var(--sa-border);
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            display: none;
            max-height: 360px;
            overflow: auto;
        }
        .sa-search-results.open { display: block; }
        .sa-search-results a {
            display: block;
            padding: 0.5rem 0.75rem;
            color: var(--sa-text);
            text-decoration: none;
        }
        .sa-search-results a:hover { background: rgba(56, 189, 248, 0.12); }
        .sa-alert-item {
            border-left: 3px solid var(--sa-accent);
            padding-left: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .sa-alert-item.warning { border-left-color: var(--sa-warn); }
        .sa-alert-item.danger { border-left-color: var(--sa-danger); }
        .sa-alert-item.info { border-left-color: var(--sa-accent); }
        .sa-activity-item { padding: 0.65rem 0; border-bottom: 1px solid var(--sa-border); }
        .sa-activity-item:last-child { border-bottom: 0; }
        .btn-group-actions .btn { margin: 0 0.15rem 0.25rem 0; }
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
        <a href="{{ route('superadmin.super-admins.index') }}" class="{{ request()->routeIs('superadmin.super-admins.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield mr-2"></i> Super Admins
        </a>
        <a href="{{ route('superadmin.settings.edit') }}" class="{{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog mr-2"></i> Settings
        </a>
        <a href="{{ route('superadmin.search.index') }}" class="{{ request()->routeIs('superadmin.search.*') ? 'active' : '' }}">
            <i class="fas fa-search mr-2"></i> Search
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
            <div class="d-flex align-items-center flex-wrap" style="gap: 0.75rem;">
                <form action="{{ route('superadmin.search.index') }}" method="GET" class="sa-search" id="sa-global-search">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search companies, users..." autocomplete="off" id="sa-search-input">
                    <div class="sa-search-results" id="sa-search-results"></div>
                </form>
                <div class="sa-muted small">{{ auth()->user()->name }}</div>
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

<script>
(function () {
    const input = document.getElementById('sa-search-input');
    const results = document.getElementById('sa-search-results');
    if (!input || !results) return;

    let timer = null;
    const suggestUrl = @json(route('superadmin.search.suggest'));

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
            results.classList.remove('open');
            results.innerHTML = '';
            return;
        }
        timer = setTimeout(async function () {
            try {
                const response = await fetch(suggestUrl + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                let html = '';
                if (data.companies && data.companies.length) {
                    html += '<div class="px-3 pt-2 sa-muted small">Companies</div>';
                    data.companies.forEach(function (item) {
                        html += '<a href="' + item.url + '"><strong>' + item.name + '</strong><div class="sa-muted small">' + (item.slug || '') + '</div></a>';
                    });
                }
                if (data.users && data.users.length) {
                    html += '<div class="px-3 pt-2 sa-muted small">Users</div>';
                    data.users.forEach(function (item) {
                        html += '<a href="' + item.url + '"><strong>' + item.name + '</strong><div class="sa-muted small">' + item.email + (item.company ? ' · ' + item.company : '') + '</div></a>';
                    });
                }
                if (!html) {
                    html = '<div class="px-3 py-2 sa-muted">No matches</div>';
                }
                results.innerHTML = html;
                results.classList.add('open');
            } catch (e) {
                results.classList.remove('open');
            }
        }, 220);
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('#sa-global-search')) {
            results.classList.remove('open');
        }
    });
})();
</script>
@stack('scripts')
</body>
</html>

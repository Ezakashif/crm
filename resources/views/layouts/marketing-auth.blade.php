<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0284c7">

    <x-marketing.seo
        :title="$title ?? 'Sign in'"
        :description="$description ?? ('Secure access to '.config('marketing.name').' CRM')"
        :robots="$robots ? 'index,follow' : 'noindex,nofollow'"
    />

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/marketing.css', 'resources/js/marketing.js'])
</head>
<body class="marketing-body antialiased">
    <a class="mk-skip-link" href="#main-content">Skip to content</a>

    <div class="min-h-screen mk-atmosphere">
        <header class="mk-container flex items-center justify-between gap-4 py-5">
            <x-marketing.logo />
            <a href="{{ route('marketing.home') }}" class="mk-nav-link">Back to website</a>
        </header>

        <main id="main-content" class="mk-container flex flex-col items-center pb-16 pt-4 sm:pt-10">
            <div @class([
                'mk-card mk-fade-up w-full p-6 sm:p-8',
                'max-w-lg' => $wide,
                'max-w-md' => ! $wide,
            ])>
                @isset($heading)
                    <h1 class="mk-display mb-2 text-2xl">{{ $heading }}</h1>
                @endisset
                @isset($subheading)
                    <p class="mk-lead mb-6 text-sm">{{ $subheading }}</p>
                @endisset

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>

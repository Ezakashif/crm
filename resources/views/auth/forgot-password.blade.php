<x-marketing-auth-layout
    title="Forgot password"
    heading="Forgot your password?"
    subheading="Enter your email and we’ll send a reset link."
>
    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
        @csrf

        <div>
            <label for="email" class="mk-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mk-input @error('email') border-red-400 @enderror"
                placeholder="you@company.com"
            >
            @error('email')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <x-marketing.button type="submit" class="w-full" size="lg">
            Email reset link
        </x-marketing.button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        <a href="{{ route('login') }}" class="font-semibold text-sky-700 hover:text-sky-800">Back to sign in</a>
    </p>
</x-marketing-auth-layout>

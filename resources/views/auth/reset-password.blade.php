<x-marketing-auth-layout
    title="Reset password"
    heading="Reset password"
    subheading="Choose a new password for your Algos account. You’ll sign in again after resetting."
>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mk-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
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

        <div>
            <label for="password" class="mk-label">New password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="mk-input @error('password') border-red-400 @enderror"
                placeholder="••••••••"
            >
            <p class="mt-1.5 text-xs text-slate-500">Use at least 8 characters.</p>
            @error('password')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mk-label">Confirm password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="mk-input @error('password_confirmation') border-red-400 @enderror"
                placeholder="••••••••"
            >
            @error('password_confirmation')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <x-marketing.button type="submit" class="w-full" size="lg">
            Reset password
        </x-marketing.button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        <a href="{{ route('password.request') }}" class="font-semibold text-sky-700 hover:text-sky-800">Request a new reset link</a>
        <span class="mx-2 text-slate-300">·</span>
        <a href="{{ route('login') }}" class="font-semibold text-sky-700 hover:text-sky-800">Back to sign in</a>
    </p>
</x-marketing-auth-layout>

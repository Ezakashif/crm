<x-marketing-auth-layout
    title="Confirm password"
    heading="Confirm password"
    subheading="This is a secure area. Please confirm your password before continuing."
>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" novalidate>
        @csrf

        <div>
            <label for="password" class="mk-label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="current-password"
                class="mk-input @error('password') border-red-400 @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <x-marketing.button type="submit" class="w-full" size="lg">
            Confirm
        </x-marketing.button>
    </form>
</x-marketing-auth-layout>

<x-marketing-auth-layout
    title="Verify email"
    heading="Verify your email"
    subheading="Thanks for signing up. Please confirm your email address to continue."
>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <p class="mb-6 text-sm leading-relaxed text-slate-600">
        Click the link we emailed you. If it hasn’t arrived, we can send another.
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
        @csrf
        <x-marketing.button type="submit" class="w-full" size="lg">
            Resend verification email
        </x-marketing.button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <x-marketing.button type="submit" variant="secondary" class="w-full">
            Log out
        </x-marketing.button>
    </form>
</x-marketing-auth-layout>

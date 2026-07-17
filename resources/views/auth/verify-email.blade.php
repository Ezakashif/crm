<x-marketing-auth-layout
    title="Verify email"
    heading="Verify your email"
    subheading="Confirm your email address to unlock your Algos workspace."
>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            A new verification link has been sent{{ ! empty($email) ? ' to '.$email : '' }}.
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-600">
        We sent a secure link to
        <span class="font-semibold text-slate-900">{{ $email ?? 'your email address' }}</span>.
        Open it to finish setup. Links expire for security—request another if needed.
    </div>

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

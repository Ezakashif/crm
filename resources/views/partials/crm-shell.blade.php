@php
    $statusToasts = [
        'profile-updated' => ['type' => 'success', 'message' => 'Profile saved.'],
        'password-updated' => ['type' => 'success', 'message' => 'Password updated.'],
        'photo-updated' => ['type' => 'success', 'message' => 'Photo updated.'],
        'photo-removed' => ['type' => 'success', 'message' => 'Photo removed.'],
        'verification-link-sent' => ['type' => 'success', 'message' => 'A new verification link has been sent.'],
    ];

    $statusKey = session('status');
    $statusFlash = is_string($statusKey) && isset($statusToasts[$statusKey])
        ? $statusToasts[$statusKey]
        : null;

    $flashes = collect([
        'success' => session('success'),
        'error' => session('error') ?? session('danger'),
        'warning' => session('warning'),
        'info' => ($statusFlash === null && filled(session('status')) && is_string(session('status')))
            ? session('status')
            : session('info'),
    ])->filter(fn ($message) => filled($message) && is_string($message))
        ->map(fn ($message, $type) => ['type' => $type === 'error' ? 'error' : $type, 'message' => $message])
        ->values();

    if ($statusFlash) {
        $flashes = $flashes->push($statusFlash);
    }
@endphp

<div id="crm-toast-stack" class="crm-toast-stack" aria-live="polite" aria-relevant="additions"></div>

<div
    id="crm-confirm-backdrop"
    class="crm-confirm-backdrop"
    hidden
    role="dialog"
    aria-modal="true"
    aria-labelledby="crm-confirm-title"
    aria-describedby="crm-confirm-message"
>
    <div class="crm-confirm">
        <h2 id="crm-confirm-title" class="crm-confirm__title">Are you sure?</h2>
        <p id="crm-confirm-message" class="crm-confirm__message">This action cannot be undone.</p>
        <div class="crm-confirm__actions">
            <button type="button" class="btn btn-default" data-crm-confirm-cancel>Cancel</button>
            <button type="button" class="btn btn-danger" data-crm-confirm-ok>Confirm</button>
        </div>
    </div>
</div>

<script type="application/json" id="crm-flash-data">@json($flashes)</script>

@props(['lead'])

<div class="btn-group btn-group-sm">
    @if($lead->callUrl())
        <a href="{{ $lead->callUrl() }}" class="btn btn-primary">
            <i class="fas fa-phone"></i> Call
        </a>
    @else
        <button type="button" class="btn btn-primary" disabled title="No phone number on file">
            <i class="fas fa-phone"></i> Call
        </button>
    @endif

    @if($lead->whatsAppUrl())
        <a href="{{ $lead->whatsAppUrl() }}" target="_blank" rel="noopener" class="btn btn-success">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    @else
        <button type="button" class="btn btn-success" disabled title="No phone number for WhatsApp">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </button>
    @endif

    @if($lead->emailUrl())
        <a href="{{ $lead->emailUrl() }}" class="btn btn-info">
            <i class="fas fa-envelope"></i> Email
        </a>
    @else
        <button type="button" class="btn btn-info" disabled title="No email address on file">
            <i class="fas fa-envelope"></i> Email
        </button>
    @endif
</div>

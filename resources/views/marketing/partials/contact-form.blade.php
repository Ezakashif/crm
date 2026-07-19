@php
    $isDemo = ($intent ?? request('intent')) === 'demo';
    $defaultMessage = $isDemo
        ? 'Hi—I’d like to book a demo of Algos for our team.'
        : old('message');
@endphp

@if (session('status'))
    <div class="alert alert-success" role="status">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('marketing.contact.store') }}" class="php-email-form" novalidate>
    @csrf
    <input type="hidden" name="intent" value="{{ old('intent', $intent ?? request('intent')) }}">

    <div class="mb-3">
        <label for="contactName" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            id="contactName"
            value="{{ old('name') }}"
            placeholder="Enter your full name"
            autocomplete="name"
            required
        >
        @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="contactEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            id="contactEmail"
            value="{{ old('email') }}"
            placeholder="Enter your email address"
            autocomplete="email"
            required
        >
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="contactCompany" class="form-label">Company</label>
        <input
            type="text"
            class="form-control @error('company') is-invalid @enderror"
            name="company"
            id="contactCompany"
            value="{{ old('company') }}"
            placeholder="Enter your company"
            autocomplete="organization"
        >
        @error('company')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="contactPhone" class="form-label">Phone Number</label>
        <input
            type="tel"
            class="form-control @error('phone') is-invalid @enderror"
            name="phone"
            id="contactPhone"
            value="{{ old('phone') }}"
            placeholder="Enter your phone number"
            autocomplete="tel"
        >
        @error('phone')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="contactMessage" class="form-label">Your Message <span class="text-danger">*</span></label>
        <textarea
            class="form-control message-textarea @error('message') is-invalid @enderror"
            name="message"
            id="contactMessage"
            rows="5"
            placeholder="Enter your message"
            required
        >{{ $defaultMessage }}</textarea>
        @error('message')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-btn">
        <span>{{ $isDemo ? 'Request demo' : 'Send Message' }}</span>
        <i class="bi bi-arrow-right"></i>
    </button>
</form>

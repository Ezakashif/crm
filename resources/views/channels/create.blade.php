<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Connect channel"
            subtitle="Add an inbound channel for your company."
            :breadcrumbs="[
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Channels', 'url' => route('channels.index')],
                ['label' => 'Connect'],
            ]"
        />
    </x-slot>

    <div class="card card-outline card-primary">
        <form method="POST" action="{{ route('channels.store') }}">
            @csrf
            <div class="card-body">
                <x-form-section title="Channel details" description="Choose a provider and name this connection.">
                    <div class="form-group">
                        <x-form-label for="provider" :required="true">Provider</x-form-label>
                        <select id="provider" name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="">Select provider...</option>
                            @foreach ($providers as $value => $label)
                                <option value="{{ $value }}" @selected(old('provider') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        <small class="form-text text-muted">
                            <strong>Generic Webhook</strong> is the quickest way to test the engine.
                            <strong>Facebook Lead Ads</strong> needs a Page ID, page access token, and Meta app webhook setup.
                        </small>
                    </div>

                    <div class="form-group">
                        <x-form-label for="name" :required="true">Display name</x-form-label>
                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required placeholder="e.g. Website webhook, Main Facebook Page">
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" id="facebook-page-id-group">
                        <x-form-label for="external_page_id">Facebook Page ID</x-form-label>
                        <input id="external_page_id" name="external_page_id" type="text"
                               class="form-control @error('external_page_id') is-invalid @enderror"
                               value="{{ old('external_page_id') }}" placeholder="Required for Facebook Lead Ads">
                        @error('external_page_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <x-form-label for="external_account_id">External account ID</x-form-label>
                        <input id="external_account_id" name="external_account_id" type="text"
                               class="form-control @error('external_account_id') is-invalid @enderror"
                               value="{{ old('external_account_id') }}" placeholder="Optional provider account id">
                        @error('external_account_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <x-form-label for="access_token">Access token</x-form-label>
                        <textarea id="access_token" name="access_token" rows="2"
                                  class="form-control @error('access_token') is-invalid @enderror"
                                  placeholder="Page access token for Facebook Lead Ads — stored encrypted">{{ old('access_token') }}</textarea>
                        @error('access_token')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-0">
                        <x-form-label for="webhook_secret">Webhook secret</x-form-label>
                        <input id="webhook_secret" name="webhook_secret" type="text"
                               class="form-control @error('webhook_secret') is-invalid @enderror"
                               value="{{ old('webhook_secret') }}"
                               placeholder="Leave blank to auto-generate">
                        @error('webhook_secret')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        <small class="form-text text-muted">Used to validate inbound webhook signatures. For Facebook, set <code>META_APP_SECRET</code> in the server environment.</small>
                    </div>
                </x-form-section>
            </div>
            <div class="card-footer">
                <div class="crm-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plug" aria-hidden="true"></i> Connect channel
                    </button>
                    <a href="{{ route('channels.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    @push('js')
        <script>
            (function () {
                const provider = document.getElementById('provider');
                const pageGroup = document.getElementById('facebook-page-id-group');
                const pageInput = document.getElementById('external_page_id');
                const tokenInput = document.getElementById('access_token');

                function syncFacebookFields() {
                    const isFacebook = provider.value === 'facebook_lead_ads';
                    pageGroup.style.display = isFacebook ? '' : 'none';
                    pageInput.required = isFacebook;
                    tokenInput.required = isFacebook;
                }

                provider.addEventListener('change', syncFacebookFields);
                syncFacebookFields();
            })();
        </script>
    @endpush
</x-app-layout>

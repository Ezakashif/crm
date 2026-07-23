<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Company settings</h2></x-slot>
    <div class="py-8"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PATCH')
                <div class="grid gap-5 md:grid-cols-2">
                    <div><label class="mk-label">Company name</label><input class="mk-input" name="name" value="{{ old('name', $company->name) }}" required></div>
                    <div><label class="mk-label">Email</label><input class="mk-input" type="email" name="email" value="{{ old('email', $company->email) }}"></div>
                    <div><label class="mk-label">Phone</label><input class="mk-input" name="phone" value="{{ old('phone', $company->phone) }}"></div>
                    <div><label class="mk-label">Logo</label><input type="file" name="logo" accept="image/*">@if($company->logoUrl())<label class="block mt-2 text-sm"><input type="checkbox" name="remove_logo" value="1"> Remove logo</label>@endif</div>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div><label class="mk-label">Address line 1</label><input class="mk-input" name="address_line_1" value="{{ old('address_line_1', $company->address_line_1) }}"></div>
                    <div><label class="mk-label">Address line 2</label><input class="mk-input" name="address_line_2" value="{{ old('address_line_2', $company->address_line_2) }}"></div>
                    <div><label class="mk-label">City</label><input class="mk-input" name="city" value="{{ old('city', $company->city) }}"></div>
                    <div><label class="mk-label">State / region</label><input class="mk-input" name="state" value="{{ old('state', $company->state) }}"></div>
                    <div><label class="mk-label">Postal code</label><input class="mk-input" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}"></div>
                    <div><label class="mk-label">Country code</label><input class="mk-input" name="country" maxlength="2" value="{{ old('country', $company->country) }}"></div>
                    <div><label class="mk-label">Timezone</label><input class="mk-input" name="timezone" value="{{ old('timezone', $company->timezone) }}" placeholder="UTC"></div>
                    <div><label class="mk-label">Currency</label><input class="mk-input" name="currency" maxlength="3" value="{{ old('currency', $company->currency) }}" placeholder="USD"></div>
                </div>
                <button class="btn btn-primary">Save company settings</button>
            </form>
        </div>
    </div></div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="crm-page-title">Create Customer</h1>
            <span class="crm-page-subtitle">Add a customer account.</span>
        </div>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Customer Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input id="company_name" name="company_name" type="text" class="form-control"
                           value="{{ old('company_name') }}">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Customer
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

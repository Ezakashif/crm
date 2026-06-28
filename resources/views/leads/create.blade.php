<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Create Lead</h1>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('leads.store') }}">
            @csrf
            <input type="hidden" name="status" value="new">
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input id="company" name="company" type="text" class="form-control" value="{{ old('company') }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="source">Source</label>
                            <select id="source" name="source" class="form-control">
                                <option value="">— Select —</option>
                                @foreach(['website', 'facebook', 'referral', 'whatsapp', 'linkedin', 'cold_call'] as $source)
                                    <option value="{{ $source }}" @selected(old('source') === $source)>{{ ucfirst(str_replace('_', ' ', $source)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="assigned_to">Assign To</label>
                            <select id="assigned_to" name="assigned_to" class="form-control">
                                <option value="">— Unassigned —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_value">Estimated Value</label>
                            <input id="estimated_value" name="estimated_value" type="number" step="0.01" class="form-control"
                                   value="{{ old('estimated_value') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="follow_up_date">Follow Up Date</label>
                            <input id="follow_up_date" name="follow_up_date" type="date" class="form-control"
                                   value="{{ old('follow_up_date') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Lead
                </button>
                <a href="{{ route('leads.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

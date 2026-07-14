<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="crm-page-title">Edit Lead</h1>
                <span class="crm-page-subtitle">Update lead details and assignment.</span>
            </div>
            <div class="crm-header-actions mt-2 mt-md-0">
                <a href="{{ route('leads.show', $lead) }}" class="btn btn-default btn-sm">
                    <i class="fas fa-eye"></i> View Lead
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card card-primary">
        <form method="POST" action="{{ route('leads.update', $lead) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $lead->name) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $lead->email) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone', $lead->phone) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input id="company" name="company" type="text" class="form-control" value="{{ old('company', $lead->company) }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="source">Source</label>
                            <select id="source" name="source" class="form-control">
                                <option value="">— Select —</option>
                                @foreach(\App\Models\Lead::SOURCES as $source)
                                    <option value="{{ $source }}" @selected(old('source', $lead->source) === $source)>
                                        {{ ucfirst(str_replace('_', ' ', $source)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                @foreach(\App\Models\Lead::manuallyAssignableStatuses($lead->status) as $status => $label)
                                    <option value="{{ $status }}" @selected(old('status', $lead->status) === $status)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @else
                                @if($lead->status !== 'won')
                                    <small class="form-text text-muted">To mark as won, use Convert to Customer.</small>
                                @endif
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    @can('assign', $lead)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assigned_to">Assign To</label>
                                <select id="assigned_to" name="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                                    <option value="">— Unassigned —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to', $lead->assigned_to) == $user->id)>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To</label>
                                <input type="text" class="form-control" value="{{ $lead->assignee?->name ?? 'Unassigned' }}" disabled>
                            </div>
                        </div>
                    @endcan
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_value">Estimated Value</label>
                            <input id="estimated_value" name="estimated_value" type="number" step="0.01" class="form-control"
                                   value="{{ old('estimated_value', $lead->estimated_value) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="follow_up_date">Follow Up Date</label>
                    <input id="follow_up_date" name="follow_up_date" type="date" class="form-control"
                           value="{{ old('follow_up_date', $lead->follow_up_date ? \Carbon\Carbon::parse($lead->follow_up_date)->format('Y-m-d') : '') }}">
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Lead
                    </button>
                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    @if($lead->status !== 'won')
        @can('convert', $lead)
            <form method="POST" action="{{ route('leads.convert', $lead) }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-user-check"></i> Convert to Customer
                </button>
            </form>
        @endcan
    @endif
</x-app-layout>

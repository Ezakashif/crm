<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="m-0">Import {{ $label }}</h1>
                <small class="text-muted">Upload a CSV file to create multiple {{ strtolower($label) }}</small>
            </div>
            <a href="{{ route($indexRoute) }}" class="btn btn-default btn-sm mt-2 mt-md-0">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-primary">
                <form method="POST" action="{{ route('imports.store', $type) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="csv_file">CSV File <span class="text-danger">*</span></label>
                            <input id="csv_file" name="csv_file" type="file" accept=".csv,text/csv"
                                   class="form-control-file @error('csv_file') is-invalid @enderror" required>
                            @error('csv_file')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Max 2 MB · up to {{ $maxRows }} data rows · invalid and duplicate rows are skipped.
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-upload"></i> Import CSV
                        </button>
                        <a href="{{ route($indexRoute) }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Sample CSV</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">Required header columns:</p>
                    <code class="d-block mb-3" style="white-space: normal;">{{ implode(', ', $headers) }}</code>
                    <a href="{{ route('imports.sample', $type) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-download"></i> Download sample CSV
                    </a>
                </div>
            </div>

            @if($type === 'leads')
                <div class="alert alert-info">
                    New leads are created with status <strong>new</strong>.
                    @can('assign.leads')
                        Optional <code>assigned_to</code> accepts an existing user email.
                    @else
                        Leads will be assigned to you automatically.
                    @endcan
                </div>
            @elseif($type === 'users')
                <div class="alert alert-info">
                    <code>roles</code> accepts role slugs such as <code>sales</code> (comma-separated for multiple).
                    Password must meet the app password policy.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

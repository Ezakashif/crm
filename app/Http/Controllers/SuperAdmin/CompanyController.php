<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(array_keys(Company::STATUSES))],
        ]);

        $companies = Company::query()
            ->withCount(['users', 'leads', 'customers', 'tasks'])
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $term = $filters['search'];
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.companies.index', [
            'companies' => $companies,
            'filters' => $filters,
            'statuses' => Company::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('superadmin.companies.create', [
            'statuses' => Company::STATUSES,
        ]);
    }

    public function store(Request $request, CompanyProvisioner $provisioner): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:companies,slug'],
            'status' => ['required', Rule::in(array_keys(Company::STATUSES))],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'required_with:admin_password', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['nullable', 'required_with:admin_email', Password::defaults()],
        ]);

        $result = $provisioner->provision($validated);

        $message = 'Company created successfully.';
        if ($result['admin']) {
            $message .= ' Company admin account provisioned.';
        }

        return redirect()
            ->route('superadmin.companies.show', $result['company'])
            ->with('success', $message);
    }

    public function show(Company $company): View
    {
        $company->loadCount(['users', 'leads', 'customers', 'tasks', 'roles']);

        $admins = $company->users()
            ->whereHas('roles', fn ($query) => $query->where('slug', 'admin'))
            ->orderBy('name')
            ->get();

        return view('superadmin.companies.show', [
            'company' => $company,
            'admins' => $admins,
            'statuses' => Company::STATUSES,
        ]);
    }

    public function edit(Company $company): View
    {
        return view('superadmin.companies.edit', [
            'company' => $company,
            'statuses' => Company::STATUSES,
        ]);
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('companies', 'slug')->ignore($company->id),
            ],
            'status' => ['required', Rule::in(array_keys(Company::STATUSES))],
        ]);

        $company->update($validated);

        return redirect()
            ->route('superadmin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    public function updateStatus(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Company::STATUSES))],
        ]);

        $company->update(['status' => $validated['status']]);

        $label = Company::STATUSES[$validated['status']];

        return back()->with('success', "Company marked as {$label}.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Services\ActivityLogger;
use App\Services\CompanySettingsService;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanySettingsController extends Controller
{
    public function edit(CurrentCompany $currentCompany): View
    {
        $company = $currentCompany->get();
        abort_unless($company, 404);

        return view('company.settings.edit', compact('company'));
    }

    public function update(UpdateCompanySettingsRequest $request, CurrentCompany $currentCompany, CompanySettingsService $settings): RedirectResponse
    {
        $company = $currentCompany->get();
        abort_unless($company, 404);
        $company = $settings->update($company, $request->validated(), $request->file('logo'));

        ActivityLogger::log('company.settings_updated', $company, ['name' => $company->name]);

        return back()->with('success', 'Company settings saved.');
    }
}

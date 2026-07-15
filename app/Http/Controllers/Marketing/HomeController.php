<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Phase 3B: foundation / component library preview.
     * Phase 3C will replace this with the full marketing home page.
     */
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            if (auth()->user()->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('marketing.foundation', [
            'plans' => config('marketing.pricing.plans', []),
            'faqItems' => [
                [
                    'id' => 'trial',
                    'question' => 'Is there a free trial?',
                    'answer' => 'Yes. You can start a free trial and explore the core CRM modules before choosing a plan.',
                ],
                [
                    'id' => 'tenants',
                    'question' => 'Is Algos multi-tenant?',
                    'answer' => 'Yes. Each company gets isolated data, roles, and settings inside a shared platform.',
                ],
                [
                    'id' => 'import',
                    'question' => 'Can we import existing data?',
                    'answer' => 'CSV import and export are available for leads, customers, and users.',
                ],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('superadmin.dashboard', [
            'stats' => [
                'companies' => Company::query()->count(),
                'active_companies' => Company::query()->where('status', Company::STATUS_ACTIVE)->count(),
                'suspended_companies' => Company::query()->where('status', Company::STATUS_SUSPENDED)->count(),
                'users' => User::withoutCompanyScope()->where('is_super_admin', false)->count(),
                'leads' => Lead::withoutCompanyScope()->count(),
                'customers' => Customer::withoutCompanyScope()->count(),
                'tasks' => Task::withoutCompanyScope()->count(),
            ],
            'recentCompanies' => Company::query()->latest()->limit(5)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboard,
    ) {}

    public function index(): View
    {
        return view('dashboard', $this->dashboard->forUser(auth()->user()));
    }
}

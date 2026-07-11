<?php

use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('companies', CompanyController::class)->except(['destroy']);
        Route::patch('companies/{company}/status', [CompanyController::class, 'updateStatus'])
            ->name('companies.status');
    });

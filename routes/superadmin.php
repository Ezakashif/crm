<?php

use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\CompanyExportController;
use App\Http\Controllers\SuperAdmin\CompanyImportController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('companies/export', [CompanyExportController::class, 'csv'])
            ->middleware('throttle:30,1')
            ->name('companies.export');
        Route::get('companies/export/pdf', [CompanyExportController::class, 'pdf'])
            ->middleware('throttle:30,1')
            ->name('companies.export.pdf');

        Route::get('companies/import', [CompanyImportController::class, 'create'])
            ->name('companies.import.create');
        Route::post('companies/import', [CompanyImportController::class, 'store'])
            ->name('companies.import.store');
        Route::get('companies/import/sample', [CompanyImportController::class, 'sample'])
            ->name('companies.import.sample');

        Route::resource('companies', CompanyController::class)->except(['destroy']);
        Route::patch('companies/{company}/status', [CompanyController::class, 'updateStatus'])
            ->name('companies.status');
        Route::get('companies/{company}/pdf', [CompanyController::class, 'pdf'])
            ->middleware('throttle:30,1')
            ->name('companies.pdf');
    });

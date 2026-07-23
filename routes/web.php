<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanySettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadActivityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebsiteLeadDemoController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\CsvExportController;
use App\Http\Controllers\WebsiteLeadWebhookController;

Route::post('/webhooks/leads/website', [WebsiteLeadWebhookController::class, 'store'])
    ->middleware(['website-lead-webhook', 'throttle:website-leads'])
    ->name('webhooks.leads.website');

require __DIR__.'/marketing.php';

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified.when_required', 'active', 'company'])
    ->name('dashboard');

Route::middleware(['auth', 'verified.when_required', 'active', 'company'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::delete('/profile/sessions/others', [\App\Http\Controllers\ProfileSessionController::class, 'destroyOthers'])->name('profile.sessions.destroy-others');
    Route::delete('/profile/sessions/{session}', [\App\Http\Controllers\ProfileSessionController::class, 'destroy'])->name('profile.sessions.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/company/settings', [CompanySettingsController::class, 'edit'])->name('company.settings.edit');
    Route::patch('/company/settings', [CompanySettingsController::class, 'update'])->name('company.settings.update');

    Route::get('/search', [GlobalSearchController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('search.index');
    Route::get('/search/suggest', [GlobalSearchController::class, 'suggest'])
        ->middleware('throttle:60,1')
        ->name('search.suggest');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/imports/{type}', [CsvImportController::class, 'create'])
        ->whereIn('type', ['leads', 'customers', 'users'])
        ->name('imports.create');
    Route::post('/imports/{type}', [CsvImportController::class, 'store'])
        ->whereIn('type', ['leads', 'customers', 'users'])
        ->name('imports.store');
    Route::get('/imports/{type}/sample', [CsvImportController::class, 'sample'])
        ->whereIn('type', ['leads', 'customers', 'users'])
        ->name('imports.sample');

    Route::get('/leads/export', [CsvExportController::class, 'leads'])
        ->middleware('throttle:30,1')
        ->name('exports.leads');
    Route::get('/customers/export', [CsvExportController::class, 'customers'])
        ->middleware('throttle:30,1')
        ->name('exports.customers');
    Route::get('/tasks/export', [CsvExportController::class, 'tasks'])
        ->middleware('throttle:30,1')
        ->name('exports.tasks');
    Route::get('/users/export', [CsvExportController::class, 'users'])
        ->middleware('throttle:30,1')
        ->name('exports.users');

    Route::resource('customers', CustomerController::class);

    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])
        ->name('leads.convert');
    Route::post('/leads/board/update', [LeadController::class, 'updateBoard'])
        ->name('leads.board.update');
    Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])
        ->name('leads.activities.store');

    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/status', [TaskController::class, 'changeStatus'])
        ->name('tasks.status');
    Route::post('/tasks/board/update', [TaskController::class, 'updateBoard'])
        ->name('tasks.board.update');

    Route::resource('users', UserController::class);
    Route::post('/users/{user}/status', [UserController::class, 'changeStatus'])
        ->name('users.status');

    Route::resource('roles', RoleController::class)->except(['show']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])
        ->middleware('throttle:30,1')
        ->name('reports.export');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified.when_required', 'active', 'company', 'permission:website_lead.demo'])->group(function () {
    Route::get('/demo/website-lead', [WebsiteLeadDemoController::class, 'index'])
        ->name('demo.website-lead');
    Route::post('/demo/website-lead', [WebsiteLeadDemoController::class, 'store'])
        ->name('demo.website-lead.store');
});

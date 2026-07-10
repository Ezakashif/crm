<?php

use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\WebsiteLeadWebhookController;

Route::post('/webhooks/leads/website', [WebsiteLeadWebhookController::class, 'store'])
    ->middleware(['website-lead-webhook', 'throttle:website-leads'])
    ->name('webhooks.leads.website');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/search', [GlobalSearchController::class, 'index'])->name('search.index');
    Route::get('/search/suggest', [GlobalSearchController::class, 'suggest'])
        ->middleware('throttle:60,1')
        ->name('search.suggest');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::resource('leads', LeadController::class);

    Route::post('/leads/{lead}/convert', 
        [LeadController::class, 'convertToCustomer']
    )->name('leads.convert');

    Route::post('/leads/board/update', [LeadController::class, 'updateBoard'])
    ->name('leads.board.update');

    Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])
        ->name('leads.activities.store');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);

    Route::post('/tasks/{task}/status', [TaskController::class, 'changeStatus'])
        ->name('tasks.status');

    Route::post('/tasks/board/update', [TaskController::class, 'updateBoard'])
        ->name('tasks.board.update');
});

Route::middleware(['auth', 'permission:website_lead.demo'])->group(function () {
    Route::get('/demo/website-lead', [WebsiteLeadDemoController::class, 'index'])
        ->name('demo.website-lead');
    Route::post('/demo/website-lead', [WebsiteLeadDemoController::class, 'store'])
        ->name('demo.website-lead.store');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);

    Route::post('/users/{user}/status', [UserController::class, 'changeStatus'])
        ->name('users.status');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class)->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});
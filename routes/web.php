<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadActivityController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebsiteLeadDemoController;
use App\Http\Controllers\WebsiteLeadWebhookController;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;

Route::post('/webhooks/leads/website', [WebsiteLeadWebhookController::class, 'store'])
    ->middleware(['website-lead-webhook', 'throttle:website-leads'])
    ->name('webhooks.leads.website');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    $taskQuery = Task::visibleTo(auth()->user());

    return view('dashboard', [
        'customerCount' => Customer::count(),
        'leadCount' => Lead::count(),
        'taskCount' => (clone $taskQuery)->count(),
        'pendingTasks' => (clone $taskQuery)->where('status', 'pending')->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

Route::middleware(['auth', 'permission:demo.website-lead'])->group(function () {
    Route::get('/demo/website-lead', [WebsiteLeadDemoController::class, 'index'])
        ->name('demo.website-lead');
    Route::post('/demo/website-lead', [WebsiteLeadDemoController::class, 'store'])
        ->name('demo.website-lead.store');
});

Route::middleware(['auth', 'permission:users.manage'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);

    Route::post('/users/{user}/status', [UserController::class, 'changeStatus'])
        ->name('users.status');
});

Route::middleware(['auth', 'permission:activity-logs.view'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});
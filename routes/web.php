<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;

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
});


Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);

    Route::post('/tasks/{task}/status', [TaskController::class, 'changeStatus'])
        ->name('tasks.status');

    Route::post('/tasks/board/update', [TaskController::class, 'updateBoard'])
        ->name('tasks.board.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);

    Route::post('/users/{user}/status', [UserController::class, 'changeStatus'])
        ->name('users.status');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});
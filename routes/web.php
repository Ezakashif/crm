<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
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
});
Route::post('/tasks/{task}/status', [TaskController::class, 'changeStatus'])
    ->name('tasks.status');

    Route::post('/tasks/board/update', [TaskController::class, 'updateBoard'])
    ->name('tasks.board.update');
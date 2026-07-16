<?php

use App\Http\Controllers\Marketing\AboutController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\FeaturesController;
use App\Http\Controllers\Marketing\HomeController;
use App\Http\Controllers\Marketing\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('marketing.home');
Route::get('/features', [FeaturesController::class, 'index'])->name('marketing.features');
Route::get('/pricing', [PricingController::class, 'index'])->name('marketing.pricing');
Route::get('/about', [AboutController::class, 'index'])->name('marketing.about');
Route::get('/contact', [ContactController::class, 'create'])->name('marketing.contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('marketing.contact.store');

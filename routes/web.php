<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/layanan', [ServiceController::class, 'index'])->name('services');
Route::get('/tentang-kami', [AboutController::class, 'index'])->name('about');

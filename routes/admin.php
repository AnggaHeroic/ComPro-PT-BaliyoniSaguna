<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BusinessActivityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryPhotoController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Route reorder harus lebih dulu agar "reorder" tidak dibaca sebagai id oleh route resource.
    Route::put('categories/reorder', [ServiceCategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', ServiceCategoryController::class)->except('show');

    Route::put('activities/reorder', [BusinessActivityController::class, 'reorder'])->name('activities.reorder');
    Route::resource('activities', BusinessActivityController::class)->except('show');

    Route::put('gallery/reorder', [GalleryPhotoController::class, 'reorder'])->name('gallery.reorder');
    Route::resource('gallery', GalleryPhotoController::class)->parameters(['gallery' => 'photo'])->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::controller(PageContentController::class)->prefix('pages')->name('pages.')->group(function () {
        Route::get('{page}', 'edit')->name('edit');
        Route::put('{page}', 'update')->name('update');
        Route::delete('{page}', 'reset')->name('reset');
        Route::post('{page}/default', 'setDefault')->name('default.set');
        Route::delete('{page}/default', 'clearDefault')->name('default.clear');
        Route::post('{page}/preview/{section}', 'preview')->name('preview');
    });

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

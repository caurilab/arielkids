<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [AuthController::class, 'create'])->name('login');
        Route::post('/connexion', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/deconnexion', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('produits', ProductController::class)
            ->parameters(['produits' => 'product'])
            ->names('products')
            ->except('show');

        Route::resource('categories', CategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::patch('categories-ordre', [CategoryController::class, 'reorder'])
            ->name('categories.reorder');

        Route::resource('commandes', OrderController::class)
            ->parameters(['commandes' => 'order'])
            ->names('orders')
            ->only(['index', 'show', 'update']);

        Route::resource('medias', MediaController::class)
            ->only(['index', 'update', 'destroy']);
    });
});

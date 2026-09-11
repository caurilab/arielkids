<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/rayon/{category:slug}', CategoryController::class)->name('category.show');
Route::get('/produit/{product:slug}', ProductController::class)->name('product.show');

Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier', [CartController::class, 'store'])->name('cart.store');
Route::patch('/panier/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/commande', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/{reference}/confirmation', [CheckoutController::class, 'confirmation'])
    ->name('checkout.confirmation');

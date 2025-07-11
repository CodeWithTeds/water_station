<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Customer Authentication Routes
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login']);
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register']);
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// Customer Dashboard - Protected Routes
Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/dashboard', [CustomerAuthController::class, 'dashboard'])->name('customer.dashboard');

    Route::get('/customer/products', [OrderController::class, 'showProducts'])->name('customer.products');
    Route::get('/customer/order', [OrderController::class, 'showOrderForm'])->name('customer.order');
    Route::post('/customer/order', [OrderController::class, 'createOrder'])->name('customer.order.create');
    Route::get('/customer/history', [OrderController::class, 'history'])->name('customer.history');

    Route::get('/customer/profile', function () {
        return view('customer.profile');
    })->name('customer.profile');

    Route::get('/customer/settings', function () {
        return view('customer.settings');
    })->name('customer.settings');

    Route::get('/customer/logout', function () {
        return view('customer.logout');
    })->name('customer.logout');
    
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Admin Dashboard - Protected Routes
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
        
        // Order Management Routes
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{id}/approve', [AdminOrderController::class, 'approve'])->name('admin.orders.approve');
        Route::post('/orders/{id}/complete', [AdminOrderController::class, 'complete'])->name('admin.orders.complete');
        Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
        
        // Admin Sales POS (walk-in) - Protected Routes
        Route::get('/sales/create', [\App\Http\Controllers\Admin\SalesController::class, 'create'])->name('admin.sales.create');
        Route::post('/sales', [\App\Http\Controllers\Admin\SalesController::class, 'store'])->name('admin.sales.store');
        Route::resource('/sales', \App\Http\Controllers\Admin\SalesController::class, [
            'as' => 'admin'
        ])->except(['create', 'store']);

        // Product Management Routes
        Route::resource('products', ProductController::class, ['as' => 'admin']);
    });
});

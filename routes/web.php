<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Supplier\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Single entry point used by the auth scaffolding and navigation: sends each role to its own dashboard.
Route::get('/dashboard', function () {
    return redirect()->route(match (auth()->user()->role) {
        'admin' => 'admin.dashboard',
        'supplier' => 'supplier.dashboard',
        default => 'customer.dashboard',
    });
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'can:isAdmin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::post('/users/store', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::patch('/users/{user}/status', [AdminController::class, 'toggleStatus'])->name('admin.users.status');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [ReportController::class, 'exportReport'])->name('admin.reports.export');
});

Route::middleware(['auth', 'can:isSupplier'])->prefix('supplier')->group(function () {
    Route::get('/dashboard', [SupplierController::class, 'dashboard'])->name('supplier.dashboard');
    Route::get('/products', [SupplierController::class, 'manageProducts'])->name('supplier.products');
    Route::post('/products/store', [SupplierController::class, 'storeProduct'])->name('supplier.products.store');
    Route::put('/products/update/{id}', [SupplierController::class, 'updateProduct'])->name('supplier.products.update');
    Route::delete('/products/delete/{id}', [SupplierController::class, 'deleteProduct'])->name('supplier.products.delete');
    Route::get('/incoming-orders', [SupplierController::class, 'incomingOrders'])->name('supplier.orders');
    Route::post('/orders/{id}/update-status', [SupplierController::class, 'updateOrderStatus'])->name('supplier.orders.update');
});

Route::middleware(['auth', 'can:isCustomer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/products', [CustomerController::class, 'products'])->name('customer.products');
    Route::get('/orders/{id}', [CustomerController::class, 'showOrder'])->name('customer.orders.show');
    Route::post('/orders', [CustomerController::class, 'store'])->name('customer.orders.store');
    
    // تم تعديل مسمى المسار هنا ليتطابق مع واجهة الـ Blade
    Route::post('/orders/{id}/cancel', [CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
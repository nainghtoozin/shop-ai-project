<?php

use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\DeliveryCategoryController as AdminDeliveryCategoryController;
use App\Http\Controllers\Admin\DeliveryTypeController as AdminDeliveryTypeController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MyOrdersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Frontend Routes
Route::get('/products', [FrontProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [FrontProductController::class, 'show'])->name('products.show');

Route::get('/categories', [FrontCategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{category:slug}', [FrontCategoryController::class, 'show'])->name('category.show');

// Cart (Session-based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Optional JSON-only endpoints (same session cart, nicer semantics)
Route::post('/ajax/cart/add/{productId}', [CartController::class, 'add'])->name('ajax.cart.add');
Route::post('/ajax/cart/update/{productId}', [CartController::class, 'update'])->name('ajax.cart.update');
Route::delete('/ajax/cart/remove/{productId}', [CartController::class, 'remove'])->name('ajax.cart.remove');
Route::post('/ajax/cart/clear', [CartController::class, 'clear'])->name('ajax.cart.clear');

// Checkout (session cart -> orders) - requires login
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::post('/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shipping-quote');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Order history
    Route::get('/my-orders', [MyOrdersController::class, 'index'])->name('my-orders.index');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/test-chart', function () {
    return view('test-chart');
})->middleware(['auth', 'verified'])->name('test.chart');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('units', UnitController::class);
    Route::patch('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('units.toggle-status');
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::get('categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
    
    // Product Routes
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

    // Order Routes
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    // Settings (admin only)
    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Payment Methods (under settings)
    Route::resource('payment-methods', AdminPaymentMethodController::class)->except(['show']);
    Route::patch('payment-methods/{payment_method}/toggle-status', [AdminPaymentMethodController::class, 'toggleStatus'])
        ->name('payment-methods.toggle-status');

    // Shipping config
    Route::resource('cities', AdminCityController::class)->except(['show']);
    Route::patch('cities/{city}/toggle-status', [AdminCityController::class, 'toggleStatus'])->name('cities.toggle-status');

    Route::resource('delivery-categories', AdminDeliveryCategoryController::class)->except(['show']);

    Route::resource('delivery-types', AdminDeliveryTypeController::class)->except(['show']);
    Route::patch('delivery-types/{delivery_type}/toggle-status', [AdminDeliveryTypeController::class, 'toggleStatus'])->name('delivery-types.toggle-status');

    // Users & Roles
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('roles', AdminRoleController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

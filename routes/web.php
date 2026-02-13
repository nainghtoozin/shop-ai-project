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
use App\Http\Controllers\Admin\HeroSliderController as AdminHeroSliderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MyOrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

Route::get('/', HomeController::class)->name('home');

// Language switcher (session-based)
Route::get('/language/{locale}', function (string $locale) {
    $supported = array_keys((array) config('localization.supported', ['en' => 'English']));
    if (!in_array($locale, $supported, true)) abort(404);

    // Only store the user's choice; middleware applies it on the next request.
    session()->put((string) config('localization.session_key', 'locale'), $locale);

    return redirect()->back();
})->name('language.switch');

// Frontend Routes
Route::get('/products', [FrontProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [FrontProductController::class, 'show'])->name('products.show');
Route::post('/products/{product:slug}/review', [FrontProductController::class, 'storeReview'])->name('products.review.store')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/{product}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
});

// Search
Route::get('/search', [FrontProductController::class, 'search'])->name('search');

Route::get('/categories', [FrontCategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{category:slug}', [FrontCategoryController::class, 'show'])->name('category.show');

// Cart (Session-based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
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

    // Hero slider (homepage)
    Route::resource('hero-sliders', AdminHeroSliderController::class)->except(['show']);
    Route::patch('hero-sliders/{hero_slider}/toggle-status', [AdminHeroSliderController::class, 'toggleStatus'])->name('hero-sliders.toggle-status');

    // Coupons
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::patch('coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

    // Users & Roles
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('roles', AdminRoleController::class)->except(['show']);

    // Reviews
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

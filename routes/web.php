<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Toko Digital Raja
|--------------------------------------------------------------------------
*/

// ==========================================
// PUBLIC ROUTES (Guest & Authenticated)
// ==========================================

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Category
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Checkout (Guest can checkout)
Route::get('/checkout/{slug}', [OrderController::class, 'checkout'])->name('checkout');
// Redirect bare GET /checkout to homepage to avoid "method not supported" error
Route::get('/checkout', function () {
    return redirect()->route('home');
});
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

// ==========================================
// GUEST ONLY ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Email Verification (Public)
Route::get('/verify-email/{token}', [VerificationController::class, 'verify'])->name('email.verify');
Route::get('/email-verification', [VerificationController::class, 'showVerificationPage'])->name('email.verify-page');

// ==========================================
// AUTHENTICATED ROUTES (All Users)
// ==========================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification (tanpa middleware EnsureEmailVerified)
    Route::post('/resend-verification', [VerificationController::class, 'resend'])->name('email.resend');
    Route::get('/verify-email-page', [VerificationController::class, 'showVerificationPage'])->name('email.verify-page');

    // Protected routes - hanya verified users
    Route::middleware('verified_email')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Order History (for buyers)
        Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    });
});

// ==========================================
// ADMIN ROUTES (Protected by verified_email middleware)
// ==========================================
Route::middleware(['auth', 'verified_email', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::put('orders/{order}/tracking', [AdminOrderController::class, 'updateTracking'])->name('orders.update-tracking');
    Route::post('orders/{order}/proof', [AdminOrderController::class, 'uploadProof'])->name('orders.proof');
    Route::delete('orders/{order}/proof', [AdminOrderController::class, 'deleteProof'])->name('orders.proof.delete');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Banners
    Route::resource('banners', BannerController::class);
    Route::put('banners/{banner}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    Route::put('announcements/{announcement}/toggle', [AnnouncementController::class, 'toggleActive'])->name('announcements.toggle');
});

// ==========================================
// SUPER ADMIN ONLY ROUTES
// ==========================================
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::resource('users', UserController::class);
    Route::put('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::put('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
});

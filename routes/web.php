<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MagazineController as AdminMagazineController;
use App\Http\Controllers\Admin\MagazinePurchaseController as AdminMagazinePurchaseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\MagazinePurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicMediaController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/media/{path}', PublicMediaController::class)
    ->where('path', 'uploads/.*')
    ->name('media.show');
Route::get('/home/products', [HomeController::class, 'products'])->name('home.products');
Route::view('/about-us', 'about')->name('about');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::redirect('/urdu-blog', '/urdu-blogs', 301);
Route::redirect('/urdu-blog/{slug}', '/urdu-blogs/{slug}', 301);
Route::redirect('/english-blog', '/english-blogs', 301);
Route::redirect('/english-blog/{slug}', '/english-blogs/{slug}', 301);
Route::get('/urdu-blogs', [BlogController::class, 'urduIndex'])->name('blogs.urdu.index');
Route::get('/urdu-blogs/{slug}', [BlogController::class, 'urduShow'])->name('blogs.urdu.show');
Route::get('/english-blogs', [BlogController::class, 'englishIndex'])->name('blogs.english.index');
Route::get('/english-blogs/{slug}', [BlogController::class, 'englishShow'])->name('blogs.english.show');

Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
Route::get('/magazines', [MagazineController::class, 'index'])->name('magazines.index');
Route::get('/magazines/{magazine}', [MagazineController::class, 'show'])->name('magazines.show');
Route::get('/magazines/{magazine}/read', [MagazineController::class, 'read'])->name('magazines.read');
Route::get('/magazines/{magazine}/download', [MagazineController::class, 'download'])->name('magazines.download');
Route::post('/magazines/{magazine}/purchase', [MagazinePurchaseController::class, 'store'])->middleware('auth')->name('magazines.purchase');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/contact-us', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact-us', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('products', AdminProductController::class)->except('show');
        Route::resource('blog-categories', AdminBlogCategoryController::class)->except('show');
        Route::resource('blogs', AdminBlogController::class)->except('show');
        Route::resource('videos', AdminVideoController::class)->except('show');
        Route::resource('magazines', AdminMagazineController::class)->except('show');
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        Route::resource('contacts', AdminContactController::class)->only(['index', 'show', 'destroy']);
        Route::get('/magazine-purchases', [AdminMagazinePurchaseController::class, 'index'])->name('magazine-purchases.index');
        Route::patch('/magazine-purchases/{magazinePurchase}', [AdminMagazinePurchaseController::class, 'update'])->name('magazine-purchases.update');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

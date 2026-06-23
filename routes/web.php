<?php

use App\Http\Controllers\Admin\MagazineBannerController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CompanySearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDetailsController;
use App\Http\Controllers\RecruitmentRequestController;
use App\Http\Controllers\SkillSuggestionController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/suggest-from-ip', [LocaleController::class, 'suggest'])
    ->middleware('throttle:30,1')
    ->name('locale.suggest');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/skill-suggestions', SkillSuggestionController::class)
    ->middleware('throttle:60,1')
    ->name('skill-suggestions');
Route::get('/magazine', [MagazineController::class, 'index'])->name('magazine.index');
Route::get('/magazine/{slug}', [MagazineController::class, 'show'])->name('magazine.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/magazine-banner', [MagazineBannerController::class, 'index'])->name('magazine-banner.index');
        Route::post('/magazine-banner', [MagazineBannerController::class, 'store'])->name('magazine-banner.store');
        Route::delete('/magazine-banner/{magazineBannerItem}', [MagazineBannerController::class, 'destroy'])->name('magazine-banner.destroy');
    });

    Route::get('/talent/profile', [ProfileDetailsController::class, 'edit'])->name('profile.details.edit');
    Route::post('/talent/profile', [ProfileDetailsController::class, 'update'])->name('profile.details.update');

    Route::get('/company/profile', [CompanyProfileController::class, 'edit'])->name('company.profile.edit');
    Route::post('/company/profile', [CompanyProfileController::class, 'update'])->name('company.profile.update');

    Route::get('/talents', [CompanySearchController::class, 'index'])->name('company.search');
    Route::get('/talents/{talent}', [CompanySearchController::class, 'show'])->name('company.talent.show');

    Route::get('/recruitment/request/{talent?}', [RecruitmentRequestController::class, 'create'])->name('recruitment.create');
    Route::post('/recruitment/request', [RecruitmentRequestController::class, 'store'])->name('recruitment.store');

    Route::post('/subscription/activate', [PaymentController::class, 'simulate'])->name('payment.simulate');
});

require __DIR__ . '/auth.php';

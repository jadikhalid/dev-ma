<?php

use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\Admin\CompanyProfileDocumentController;
use App\Http\Controllers\Admin\ProfileDocumentController;
use App\Http\Controllers\Admin\PublicationsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CompanyAccompanimentController;
use App\Http\Controllers\CompanyCatalogSearchController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CompanySearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDetailsController;
use App\Http\Controllers\RecruitmentRequestController;
use App\Http\Controllers\SkillSuggestionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TalentProfileDocumentController;
use App\Http\Controllers\TalentPresentationVideoController;
use App\Http\Controllers\TalentSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/suggest-from-ip', [LocaleController::class, 'suggest'])
    ->middleware('throttle:30,1')
    ->name('locale.suggest');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/skill-suggestions', SkillSuggestionController::class)
    ->middleware('throttle:60,1')
    ->name('skill-suggestions');
Route::get('/talent-search', TalentSearchController::class)
    ->middleware('throttle:30,1')
    ->name('talent-search');
Route::get('/company-search', CompanyCatalogSearchController::class)
    ->middleware(['auth', 'verified', 'throttle:30,1'])
    ->name('company-catalog-search');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account/pending', [AccountStatusController::class, 'pending'])
        ->middleware('account.pending')
        ->name('account.pending');
    Route::get('/account/rejected', [AccountStatusController::class, 'rejected'])
        ->middleware('account.rejected')
        ->name('account.rejected');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('account.approved')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('staff')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/registration', [UserManagementController::class, 'registration'])->name('users.registration');
        Route::get('/profile-documents/{profileDocument}', [ProfileDocumentController::class, 'show'])->name('profile-documents.show');
        Route::get('/company-profile-documents/{companyProfileDocument}', [CompanyProfileDocumentController::class, 'show'])->name('company-profile-documents.show');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::middleware('admin')->group(function () {
            Route::post('/users/{user}/moderator', [UserManagementController::class, 'grantModerator'])->name('users.moderator.grant');
            Route::delete('/users/{user}/moderator', [UserManagementController::class, 'revokeModerator'])->name('users.moderator.revoke');
            Route::post('/moderation-requests/{moderationRequest}/approve', [UserManagementController::class, 'approveRequest'])->name('moderation.approve');
            Route::post('/moderation-requests/{moderationRequest}/reject', [UserManagementController::class, 'rejectRequest'])->name('moderation.reject');
        });
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/publications', [PublicationsController::class, 'index'])->name('publications.index');
        Route::post('/publications/news', [PublicationsController::class, 'storeNews'])->name('publications.news.store');
        Route::put('/publications/news/{newsItem}', [PublicationsController::class, 'updateNews'])->name('publications.news.update');
        Route::delete('/publications/news/{newsItem}', [PublicationsController::class, 'destroyNews'])->name('publications.news.destroy');
        Route::post('/publications/social-posts', [PublicationsController::class, 'storeSocialPost'])->name('publications.social-posts.store');
        Route::delete('/publications/social-posts/{socialPost}', [PublicationsController::class, 'destroySocialPost'])->name('publications.social-posts.destroy');

        Route::redirect('/magazine-banner', '/admin/publications');
        Route::redirect('/news', '/admin/publications');
        Route::redirect('/social-posts', '/admin/publications');
        Route::redirect('/social-feed', '/admin/publications');
    });

    Route::middleware('talent.approved')->group(function () {
        Route::get('/talent/profile', [ProfileDetailsController::class, 'edit'])->name('profile.details.edit');
        Route::post('/talent/profile', [ProfileDetailsController::class, 'update'])->name('profile.details.update');
        Route::get('/talent/profile/documents/{profileDocument}', [TalentProfileDocumentController::class, 'show'])->name('profile.documents.show');
        Route::delete('/talent/profile/documents/{profileDocument}', [TalentProfileDocumentController::class, 'destroy'])->name('profile.documents.destroy');
        Route::post('/talent/presentation-video', [TalentPresentationVideoController::class, 'store'])->name('talent.presentation-video.store');
        Route::delete('/talent/presentation-video', [TalentPresentationVideoController::class, 'destroy'])->name('talent.presentation-video.destroy');
        Route::post('/subscription/activate', [PaymentController::class, 'simulate'])->name('payment.simulate');
    });

    Route::middleware('account.approved')->group(function () {
        Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
        Route::post('/inbox/conversations', [InboxController::class, 'store'])->name('inbox.store');
        Route::get('/inbox/attachments/{attachment}', [InboxController::class, 'showAttachment'])->name('inbox.attachments.show');
        Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('inbox.show');
        Route::post('/inbox/{conversation}/messages', [InboxController::class, 'storeMessage'])->name('inbox.messages.store');

        Route::get('/company/profile', [CompanyProfileController::class, 'edit'])->name('company.profile.edit');
        Route::post('/company/profile', [CompanyProfileController::class, 'update'])->name('company.profile.update');

        Route::get('/talents', [CompanySearchController::class, 'index'])->name('company.search');
        Route::get('/talents/{talent}', [CompanySearchController::class, 'show'])->name('company.talent.show');
        Route::get('/talents/{talent}/cv', [CompanySearchController::class, 'showCv'])->name('company.talent.cv');

        Route::get('/recruitment/request/{talent?}', [RecruitmentRequestController::class, 'create'])->name('recruitment.create');
        Route::post('/recruitment/request', [RecruitmentRequestController::class, 'store'])->name('recruitment.store');

        Route::post('/services/accompagnement', [CompanyAccompanimentController::class, 'store'])
            ->name('company.accompagnement.store');
    });
});
require __DIR__.'/auth.php';

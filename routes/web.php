<?php

use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\Admin\CompanyProfileDocumentController;
use App\Http\Controllers\Admin\DirectHireController as AdminDirectHireController;
use App\Http\Controllers\Admin\JobPostingController as AdminJobPostingController;
use App\Http\Controllers\Admin\ProfileDocumentController;
use App\Http\Controllers\Admin\PublicationsController;
use App\Http\Controllers\Admin\RecruitmentRequestController as AdminRecruitmentRequestController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Company\DirectHireController as CompanyDirectHireController;
use App\Http\Controllers\CompanyAccompanimentController;
use App\Http\Controllers\CompanyCatalogSearchController;
use App\Http\Controllers\CompanyJobController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CompanySearchController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ModeratorModeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDetailsController;
use App\Http\Controllers\RecruitmentRequestController;
use App\Http\Controllers\SkillSuggestionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Talent\DirectHireController as TalentDirectHireController;
use App\Http\Controllers\TalentJobController;
use App\Http\Controllers\TalentProfileDocumentController;
use App\Http\Controllers\TalentPresentationVideoController;
use App\Http\Controllers\TalentSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/suggest-from-ip', [LocaleController::class, 'suggest'])
    ->middleware('throttle:30,1')
    ->name('locale.suggest');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile/email/confirm/{token}', [ProfileController::class, 'confirmPendingEmail'])
    ->middleware('throttle:20,1')
    ->name('profile.email.confirm');
Route::get('/skill-suggestions', SkillSuggestionController::class)
    ->middleware('throttle:60,1')
    ->name('skill-suggestions');
Route::get('/talent-search', TalentSearchController::class)
    ->middleware('throttle:30,1')
    ->name('talent-search');
Route::get('/company-search', CompanyCatalogSearchController::class)
    ->middleware(['auth', 'verified', 'throttle:30,1'])
    ->name('company-catalog-search');
Route::get('/services', [ServiceController::class, 'index'])
    ->middleware(['auth', 'verified', 'account.approved'])
    ->name('services.index');

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
    Route::patch('/profile/contact', [ProfileController::class, 'updateContact'])->name('profile.contact.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/email/pending/cancel', [ProfileController::class, 'cancelPendingEmail'])->name('profile.email.cancel');

    Route::post('/moderator-mode', [ModeratorModeController::class, 'update'])
        ->middleware('account.approved')
        ->name('moderator-mode.update');

    Route::middleware('staff')->prefix('admin')->name('admin.')->group(function () {
        Route::middleware('moderator.permission:accounts.view')->group(function () {
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/registration', [UserManagementController::class, 'registration'])->name('users.registration');
            Route::get('/profile-documents/{profileDocument}', [ProfileDocumentController::class, 'show'])->name('profile-documents.show');
            Route::get('/company-profile-documents/{companyProfileDocument}', [CompanyProfileDocumentController::class, 'show'])->name('company-profile-documents.show');
        });

        Route::middleware('admin')->group(function () {
            Route::get('/users/check-email', [UserManagementController::class, 'checkEmail'])
                ->middleware('throttle:30,1')
                ->name('users.check-email');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::post('/users/{user}/moderator', [UserManagementController::class, 'grantModerator'])->name('users.moderator.grant');
            Route::put('/users/{user}/moderator/permissions', [UserManagementController::class, 'updateModeratorPermissions'])->name('users.moderator.permissions');
            Route::delete('/users/{user}/moderator', [UserManagementController::class, 'revokeModerator'])->name('users.moderator.revoke');
        });

        Route::post('/users/{user}/approve', [UserManagementController::class, 'approve'])
            ->middleware('moderator.permission:accounts.approve')
            ->name('users.approve');
        Route::post('/users/{user}/reject', [UserManagementController::class, 'reject'])
            ->middleware('moderator.permission:accounts.reject')
            ->name('users.reject');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
            ->middleware('moderator.permission:accounts.delete')
            ->name('users.destroy');

        Route::middleware('moderator.permission:sourcing.manage')->group(function () {
            Route::get('/recruitment', [AdminRecruitmentRequestController::class, 'index'])->name('recruitment.index');
            Route::get('/recruitment/{recruitmentRequest}', [AdminRecruitmentRequestController::class, 'show'])->name('recruitment.show');
            Route::post('/recruitment/{recruitmentRequest}/messages', [AdminRecruitmentRequestController::class, 'storeMessage'])->name('recruitment.messages.store');
            Route::patch('/recruitment/{recruitmentRequest}/status', [AdminRecruitmentRequestController::class, 'updateStatus'])->name('recruitment.status');
        });

        Route::middleware('moderator.permission:direct_hire.manage')->group(function () {
            Route::get('/direct-hire', [AdminDirectHireController::class, 'index'])->name('direct-hire.index');
            Route::get('/direct-hire/talent-search', [AdminDirectHireController::class, 'searchTalents'])->name('direct-hire.talent-search');
            Route::get('/direct-hire/company-search', [AdminDirectHireController::class, 'searchCompanies'])->name('direct-hire.company-search');
            Route::get('/direct-hire/talents/{talent}/create', [AdminDirectHireController::class, 'create'])->name('direct-hire.create');
            Route::get('/direct-hire/talents/{talent}/profile', [AdminDirectHireController::class, 'showTalentProfile'])->name('direct-hire.talent-profile');
            Route::post('/direct-hire/talents/{talent}', [AdminDirectHireController::class, 'store'])->name('direct-hire.store');
            Route::get('/direct-hire/{directHire}', [AdminDirectHireController::class, 'show'])->name('direct-hire.show');
            Route::post('/direct-hire/{directHire}/messages', [AdminDirectHireController::class, 'storeMessage'])->name('direct-hire.messages.store');
            Route::post('/direct-hire/{directHire}/rounds', [AdminDirectHireController::class, 'storeRound'])->name('direct-hire.rounds.store');
            Route::patch('/direct-hire/{directHire}/rounds/{round}', [AdminDirectHireController::class, 'updateRound'])->name('direct-hire.rounds.update');
            Route::post('/direct-hire/{directHire}/rounds/{round}/cancel', [AdminDirectHireController::class, 'cancelRound'])->name('direct-hire.rounds.cancel');
            Route::post('/direct-hire/{directHire}/close', [AdminDirectHireController::class, 'close'])->name('direct-hire.close');
            Route::post('/direct-hire/{directHire}/unlock-talent', [AdminDirectHireController::class, 'unlockTalent'])->name('direct-hire.unlock-talent');
            Route::post('/direct-hire/{directHire}/deferral', [AdminDirectHireController::class, 'respondToDeferral'])->name('direct-hire.deferral');
            Route::post('/direct-hire/{directHire}/withdraw', [AdminDirectHireController::class, 'withdraw'])->name('direct-hire.withdraw');
        });

        Route::middleware('moderator.permission:jobs.manage')->group(function () {
            Route::get('/jobs', [AdminJobPostingController::class, 'index'])->name('jobs.index');
            Route::get('/jobs/{job}', [AdminJobPostingController::class, 'show'])->name('jobs.show');
            Route::get('/jobs/{job}/edit', [AdminJobPostingController::class, 'edit'])->name('jobs.edit');
            Route::put('/jobs/{job}', [AdminJobPostingController::class, 'update'])->name('jobs.update');
            Route::post('/jobs/{job}/publish', [AdminJobPostingController::class, 'publish'])->name('jobs.publish');
            Route::post('/jobs/{job}/close', [AdminJobPostingController::class, 'close'])->name('jobs.close');
            Route::post('/jobs/{job}/hide', [AdminJobPostingController::class, 'hide'])->name('jobs.hide');
            Route::delete('/jobs/{job}', [AdminJobPostingController::class, 'destroy'])->name('jobs.destroy');
            Route::patch('/jobs/{job}/applications/{application}', [AdminJobPostingController::class, 'updateApplication'])->name('jobs.applications.update');
        });

        Route::middleware('moderator.permission:publications.manage')->group(function () {
            Route::get('/publications', [PublicationsController::class, 'index'])->name('publications.index');
            Route::post('/publications/news', [PublicationsController::class, 'storeNews'])->name('publications.news.store');
            Route::put('/publications/news/{newsItem}', [PublicationsController::class, 'updateNews'])->name('publications.news.update');
            Route::delete('/publications/news/{newsItem}', [PublicationsController::class, 'destroyNews'])->name('publications.news.destroy');
            Route::post('/publications/social-posts', [PublicationsController::class, 'storeSocialPost'])->name('publications.social-posts.store');
            Route::put('/publications/social-posts/{socialPost}', [PublicationsController::class, 'updateSocialPost'])->name('publications.social-posts.update');
            Route::delete('/publications/social-posts/{socialPost}', [PublicationsController::class, 'destroySocialPost'])->name('publications.social-posts.destroy');

            Route::redirect('/magazine-banner', '/admin/publications');
            Route::redirect('/news', '/admin/publications');
            Route::redirect('/social-posts', '/admin/publications');
            Route::redirect('/social-feed', '/admin/publications');
        });
    });

    Route::middleware('talent.approved')->group(function () {
        Route::get('/talent/profile', [ProfileDetailsController::class, 'edit'])->name('profile.details.edit');
        Route::post('/talent/profile', [ProfileDetailsController::class, 'update'])->name('profile.details.update');
        Route::get('/talent/profile/documents/{profileDocument}', [TalentProfileDocumentController::class, 'show'])->name('profile.documents.show');
        Route::delete('/talent/profile/documents/{profileDocument}', [TalentProfileDocumentController::class, 'destroy'])->name('profile.documents.destroy');
        Route::post('/talent/presentation-video', [TalentPresentationVideoController::class, 'store'])->name('talent.presentation-video.store');
        Route::delete('/talent/presentation-video', [TalentPresentationVideoController::class, 'destroy'])->name('talent.presentation-video.destroy');
        Route::post('/subscription/activate', [PaymentController::class, 'simulate'])->name('payment.simulate');

        Route::get('/jobs', [TalentJobController::class, 'index'])->name('talent.jobs.index');
        Route::get('/jobs/{job}', [TalentJobController::class, 'show'])->name('talent.jobs.show');
        Route::post('/jobs/{job}/apply', [TalentJobController::class, 'apply'])->name('talent.jobs.apply');

        Route::get('/talent/direct-hire', [TalentDirectHireController::class, 'index'])->name('talent.direct-hire.index');
        Route::get('/talent/direct-hire/{directHire}', [TalentDirectHireController::class, 'show'])->name('talent.direct-hire.show');
        Route::post('/talent/direct-hire/{directHire}/decide', [TalentDirectHireController::class, 'decide'])->name('talent.direct-hire.decide');
        Route::post('/talent/direct-hire/{directHire}/messages', [TalentDirectHireController::class, 'storeMessage'])->name('talent.direct-hire.messages.store');
    });

    Route::middleware('account.approved')->group(function () {
        Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
        Route::post('/inbox/conversations', [InboxController::class, 'store'])->name('inbox.store');
        Route::get('/inbox/attachments/{attachment}', [InboxController::class, 'showAttachment'])->name('inbox.attachments.show');
        Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('inbox.show');
        Route::post('/inbox/{conversation}/messages', [InboxController::class, 'storeMessage'])->name('inbox.messages.store');

        Route::middleware('company.owner')->group(function () {
            Route::get('/company/profile', [CompanyProfileController::class, 'edit'])->name('company.profile.edit');
            Route::post('/company/profile', [CompanyProfileController::class, 'update'])->name('company.profile.update');
            Route::post('/company/users', [CompanyUserController::class, 'store'])->name('company.users.store');
            Route::put('/company/users/{member}', [CompanyUserController::class, 'update'])->name('company.users.update');
            Route::delete('/company/users/{member}', [CompanyUserController::class, 'destroy'])->name('company.users.destroy');
            Route::post('/services/accompagnement', [CompanyAccompanimentController::class, 'store'])
                ->name('company.accompagnement.store');
        });

        Route::get('/talents', [CompanySearchController::class, 'index'])->name('company.search');
        Route::get('/talents/{talent}', [CompanySearchController::class, 'show'])->name('company.talent.show');
        Route::get('/talents/{talent}/cv', [CompanySearchController::class, 'showCv'])->name('company.talent.cv');

        Route::get('/company/direct-hire', [CompanyDirectHireController::class, 'index'])->name('company.direct-hire.index');
        Route::get('/talents/{talent}/direct-hire', [CompanyDirectHireController::class, 'create'])->name('company.direct-hire.create');
        Route::post('/talents/{talent}/direct-hire', [CompanyDirectHireController::class, 'store'])->name('company.direct-hire.store');
        Route::get('/company/direct-hire/{directHire}', [CompanyDirectHireController::class, 'show'])->name('company.direct-hire.show');
        Route::post('/company/direct-hire/{directHire}/messages', [CompanyDirectHireController::class, 'storeMessage'])->name('company.direct-hire.messages.store');
        Route::post('/company/direct-hire/{directHire}/rounds', [CompanyDirectHireController::class, 'storeRound'])->name('company.direct-hire.rounds.store');
        Route::patch('/company/direct-hire/{directHire}/rounds/{round}', [CompanyDirectHireController::class, 'updateRound'])->name('company.direct-hire.rounds.update');
        Route::post('/company/direct-hire/{directHire}/rounds/{round}/cancel', [CompanyDirectHireController::class, 'cancelRound'])->name('company.direct-hire.rounds.cancel');
        Route::post('/company/direct-hire/{directHire}/close', [CompanyDirectHireController::class, 'close'])->name('company.direct-hire.close');
        Route::post('/company/direct-hire/{directHire}/unlock-talent', [CompanyDirectHireController::class, 'unlockTalent'])->name('company.direct-hire.unlock-talent');
        Route::post('/company/direct-hire/{directHire}/deferral', [CompanyDirectHireController::class, 'respondToDeferral'])->name('company.direct-hire.deferral');
        Route::post('/company/direct-hire/{directHire}/withdraw', [CompanyDirectHireController::class, 'withdraw'])->name('company.direct-hire.withdraw');

        Route::get('/recruitment/request/{talent?}', [RecruitmentRequestController::class, 'create'])->name('recruitment.create');
        Route::post('/recruitment/request', [RecruitmentRequestController::class, 'store'])->name('recruitment.store');
        Route::get('/sourcing', [RecruitmentRequestController::class, 'index'])->name('sourcing.index');
        Route::get('/sourcing/{recruitmentRequest}', [RecruitmentRequestController::class, 'show'])->name('sourcing.show');
        Route::post('/sourcing/{recruitmentRequest}/unlock-talent', [RecruitmentRequestController::class, 'unlockTalent'])->name('sourcing.unlock-talent');
        Route::post('/sourcing/{recruitmentRequest}/messages', [RecruitmentRequestController::class, 'storeMessage'])->name('sourcing.messages.store');

        Route::middleware('company.jobs')->prefix('company/jobs')->name('company.jobs.')->group(function () {
            Route::get('/', [CompanyJobController::class, 'index'])->name('index');
            Route::get('/create', [CompanyJobController::class, 'create'])->name('create');
            Route::post('/', [CompanyJobController::class, 'store'])->name('store');
            Route::get('/{job}', [CompanyJobController::class, 'show'])->name('show');
            Route::get('/{job}/edit', [CompanyJobController::class, 'edit'])->name('edit');
            Route::put('/{job}', [CompanyJobController::class, 'update'])->name('update');
            Route::post('/{job}/publish', [CompanyJobController::class, 'publish'])->name('publish');
            Route::post('/{job}/close', [CompanyJobController::class, 'close'])->name('close');
            Route::post('/{job}/hide', [CompanyJobController::class, 'hide'])->name('hide');
            Route::patch('/{job}/applications/{application}', [CompanyJobController::class, 'updateApplication'])->name('applications.update');
        });
    });
});
require __DIR__.'/auth.php';

<div id="account-profile-card" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @include('profile.partials.update-profile-information-form')
</div>

@if (Auth::user()->isCompanyOwner())
    <div id="account-contact-card" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        @include('profile.partials.update-company-contact-form')
    </div>

    @if (Auth::user()->canManageCompanyUsers())
        @include('profile.partials.company-users-panel')
    @endif
@endif

<div id="account-password-card" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @include('profile.partials.update-password-form')
</div>

<div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6 sm:p-8">
    @include('profile.partials.delete-user-form')
</div>

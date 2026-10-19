@extends('common::web.layouts.app')

@section('title', __('common::sidebar.profile'))

@section('app_content')
<div class="container-fluid px-0">
    <!-- Profile Header / Identity -->
    <div class="d-flex align-items-center gap-3 gap-md-4 mb-4 mb-md-5">
        <!-- Avatar with Edit Badge -->
        <div class="position-relative d-inline-block flex-shrink-0">
            <img src="{{ $user->image ?? ($user->isTeacher() ? web_asset('images/teacher.jpg') : web_asset('images/student.jpg')) }}" 
                 alt="{{ $user->name }}" 
                 class="rounded-circle object-fit-cover border border-3 border-white shadow-sm"
                 width="84" height="84">
            <button type="button" class="btn btn-white btn-sm rounded-circle shadow-sm position-absolute bottom-0 end-0 p-1 d-flex align-items-center justify-content-center border" data-bs-toggle="modal" data-bs-target="#editProfileModal" title="{{ __('user::profile.change_photo') }}" aria-label="{{ __('user::profile.change_photo') }}">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </button>
        </div>

        <!-- User Account Info -->
        <div>
            <h1 class="h2 fw-bold text-primary mb-1">
                {{ $user->isTeacher() ? __('user::profile.teacher_account') : ($user->isStudent() ? __('user::profile.student_account') : $user->name) }}
            </h1>
            <p class="text-secondary mb-0 fw-medium">
                {{ '@' . ($user->username ?? '') }}
            </p>
        </div>
    </div>

    <!-- Cards Row: About You + Account Settings -->
    <div class="row g-4 mb-4">
        <!-- About you Card -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 h-100">
                <h2 class="h5 fw-bold text-body mb-4">
                    {{ __('user::profile.about_you') }}
                </h2>

                <div class="row g-4">
                    <!-- Email -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::attributes.email') }}
                        </span>
                        <span class="fw-bold text-body d-block text-break">
                            {{ $user->email }}
                        </span>
                    </div>

                    <!-- Account Type -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::profile.account_type') }}
                        </span>
                        <span class="fw-bold text-body d-block">
                            {{ $user->isTeacher() ? __('user::attributes.teacher') : ($user->isStudent() ? __('user::attributes.student') : ucfirst($user->type->value ?? '')) }}
                        </span>
                    </div>

                    <!-- School -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::attributes.school') }}
                        </span>
                        <span class="fw-bold text-body d-block">
                            {{ $user->school?->title ?? '-' }}
                        </span>
                    </div>

                    <!-- City -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::attributes.city') }}
                        </span>
                        <span class="fw-bold text-body d-block">
                            {{ $user->city?->title ?? '-' }}
                        </span>
                    </div>

                    <!-- Zone / Region -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::attributes.region') }}
                        </span>
                        <span class="fw-bold text-body d-block">
                            {{ $user->region?->title ?? '-' }}
                        </span>
                    </div>

                    <!-- Country -->
                    <div class="col-12 col-sm-6">
                        <span class="small text-secondary text-uppercase fw-semibold d-block mb-1">
                            {{ __('user::attributes.country') }}
                        </span>
                        <span class="fw-bold text-body d-block">
                            {{ $user->country?->title ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account settings Card -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 h-100">
                <h2 class="h5 fw-bold text-body mb-4">
                    {{ __('user::profile.account_settings') }}
                </h2>

                <div class="list-group list-group-flush gap-2">
                    <!-- Edit profile button -->
                    <button type="button" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center justify-content-between p-3 bg-transparent text-body" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <div class="d-flex align-items-center gap-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary flex-shrink-0">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span class="fw-semibold">{{ __('user::profile.edit_profile') }}</span>
                        </div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron-arrow text-secondary">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>

                    <!-- Sign out button -->
                    <button type="submit" form="logoutForm" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center gap-3 p-3 bg-transparent text-body">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary flex-shrink-0">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="fw-semibold">{{ __('user::profile.sign_out') }}</span>
                    </button>

                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferences Card -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5">
                <h2 class="h5 fw-bold text-body mb-4">
                    {{ __('user::profile.preferences') }}
                </h2>

                <div class="list-group list-group-flush gap-2">
                    <!-- Language item -->
                    <button type="button" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center justify-content-between p-3 bg-transparent text-body" id="languageSwitchBtn" data-url="{{ route('profile.language') }}" data-locale="{{ app()->getLocale() === 'en' ? 'ar' : 'en' }}">
                        <div class="d-flex align-items-center gap-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary flex-shrink-0">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                            </svg>
                            <span class="fw-semibold">{{ __('user::profile.language') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-secondary">
                            <span class="fw-medium">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron-arrow text-secondary">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </button>

                    <!-- Theme item -->
                    <button type="button" class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center justify-content-between p-3 bg-transparent text-body" id="themeSwitchBtn" data-url="{{ route('profile.theme') }}">
                        <div class="d-flex align-items-center gap-3">
                            <svg id="themeIconSun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary flex-shrink-0 {{ session('theme', request()->cookie('app_theme', 'light')) === 'dark' ? 'd-none' : '' }}">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg id="themeIconMoon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary flex-shrink-0 {{ session('theme', request()->cookie('app_theme', 'light')) === 'dark' ? '' : 'd-none' }}">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                            <span class="fw-semibold">{{ __('user::profile.theme') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-secondary">
                            <span class="fw-medium" id="themeLabelText">{{ session('theme', request()->cookie('app_theme', 'light')) === 'dark' ? __('user::profile.dark') : __('user::profile.light') }}</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron-arrow text-secondary">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3 p-sm-4 tenant-modal">
            <div class="modal-header border-0 pb-0 d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 bg-white p-2 d-inline-flex align-items-center justify-content-center shadow-sm border">
                        <img src="{{ web_asset('images/logo.webp') }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="sidebar-logo">
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-body mb-1" id="editProfileModalLabel">
                            {{ __('user::profile.edit_profile') }}
                        </h5>
                        <p class="text-secondary small mb-0">
                            {{ tenant()?->name ?? config('app.name') }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-white bg-white rounded-circle shadow-sm p-2 text-secondary d-flex align-items-center justify-content-center border" data-bs-dismiss="modal" aria-label="{{ __('user::profile.cancel') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="editProfileForm" action="{{ route('profile.update') }}" method="POST" data-ajax>
                @csrf
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-12 col-md-6">
                            <label for="profile_name" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.name') }}
                            </label>
                            <div class="input-group rounded-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <input type="text" id="profile_name" name="name" class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3" value="{{ $user->name }}" required>
                            </div>
                            <div class="text-danger small mt-1 step-error d-none" id="name-error"></div>
                        </div>

                        <!-- Username -->
                        <div class="col-12 col-md-6">
                            <label for="profile_username" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.username') }}
                            </label>
                            <div class="input-group rounded-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                    </svg>
                                </span>
                                <input type="text" id="profile_username" name="username" class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3" value="{{ $user->username }}" required>
                            </div>
                            <div class="text-danger small mt-1 step-error d-none" id="username-error"></div>
                        </div>

                        <!-- Email (Disabled & Read-only) -->
                        <div class="col-12">
                            <label for="profile_email" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.email') }}
                            </label>
                            <div class="input-group rounded-3 opacity-75">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                                <input type="email" id="profile_email" class="form-control bg-light border-start-0 rounded-end-3 py-2 pe-3 text-secondary" value="{{ $user->email }}" disabled readonly tabindex="-1">
                            </div>
                        </div>

                        <!-- Password Change Section (Optional) -->
                        <div class="col-12 pt-3">
                            <h6 class="fw-bold text-body small text-uppercase mb-2">{{ __('user::attributes.password') }}</h6>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="profile_old_password" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.old_password') }}
                            </label>
                            <div class="input-group rounded-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="profile_old_password" name="old_password" class="form-control bg-white border-start-0 border-end-0 py-2">
                                <button type="button" class="input-group-text bg-white border-start-0 rounded-end-3 text-secondary pe-3 toggle-pwd" data-target="#profile_old_password" aria-label="{{ __('user::attributes.old_password') }}">
                                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-slash-icon d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-danger small mt-1 step-error d-none" id="old_password-error"></div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="profile_new_password" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.new_password') }}
                            </label>
                            <div class="input-group rounded-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="profile_new_password" name="new_password" class="form-control bg-white border-start-0 border-end-0 py-2">
                                <button type="button" class="input-group-text bg-white border-start-0 rounded-end-3 text-secondary pe-3 toggle-pwd" data-target="#profile_new_password" aria-label="{{ __('user::attributes.new_password') }}">
                                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-slash-icon d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-danger small mt-1 step-error d-none" id="new_password-error"></div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="profile_new_password_confirmation" class="form-label small text-secondary fw-semibold mb-2">
                                {{ __('user::attributes.new_password_confirmation') }}
                            </label>
                            <div class="input-group rounded-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="profile_new_password_confirmation" name="new_password_confirmation" class="form-control bg-white border-start-0 border-end-0 py-2">
                                <button type="button" class="input-group-text bg-white border-start-0 rounded-end-3 text-secondary pe-3 toggle-pwd" data-target="#profile_new_password_confirmation" aria-label="{{ __('user::attributes.new_password_confirmation') }}">
                                    <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-slash-icon d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-danger small mt-1 step-error d-none" id="new_password_confirmation-error"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-0 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-white bg-white rounded-pill px-4 py-2 text-secondary fw-medium shadow-sm border" data-bs-dismiss="modal">
                        {{ __('user::profile.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        {{ __('user::profile.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

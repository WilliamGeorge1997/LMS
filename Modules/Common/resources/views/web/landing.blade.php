@extends('common::web.layouts.master')

@section('title', __('common::messages.digital_learning'))
@section('main_class', 'min-vh-100 d-flex flex-column justify-content-center align-items-center py-2 px-3 position-relative')

@section('content')
<!-- Top Utility Bar: Language & Theme Switchers -->
<div class="position-absolute top-0 end-0 m-3 m-lg-4 z-3 d-flex align-items-center gap-2">
    <!-- Language Switcher -->
    <button type="button" id="languageSwitchBtn" class="btn btn-white border shadow-sm rounded-pill px-3 py-1 py-sm-2 d-inline-flex align-items-center gap-2" data-url="{{ route('profile.language') }}" title="{{ __('common::messages.switch_language') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="2" y1="12" x2="22" y2="12"></line>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
        </svg>
        <span class="small fw-medium">{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
    </button>

    <!-- Theme Switcher -->
    <button type="button" id="themeSwitchBtn" class="btn btn-white border shadow-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center back-btn" data-url="{{ route('profile.theme') }}" title="Toggle theme">
        <svg id="themeIconSun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
        <svg id="themeIconMoon" class="d-none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>
</div>

<!-- 2-Column Split Hero Layout -->
<div class="container my-auto">
    <div class="row align-items-center g-4 g-lg-5">
        <!-- Left Column: Content & Actions -->
        <div class="col-12 col-lg-6 text-center text-lg-start">
            <!-- Brand Logo (No plain title text, logo only) -->
            <div class="mb-3 mb-lg-4">
                <img src="{{ web_asset('images/logo.webp') }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="img-fluid auth-logo">
            </div>

            <!-- Enriched Headline -->
            <h1 class="display-6 fw-bold mb-3 text-body">
                {{ __('common::messages.landing_headline') }}
            </h1>

            <!-- Enriched Description -->
            <p class="lead text-secondary mb-4 mb-lg-5 fs-6 col-12 col-xl-11">
                {{ __('common::messages.landing_enriched') }}
            </p>

            <!-- Compact Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center justify-content-lg-start align-items-center gap-2 gap-sm-3">
                <!-- Desktop Button (Compact) -->
                @if(!empty($appPath))
                    <a href="{{ $appPath }}" download class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                        <span class="small fw-semibold">{{ __('common::messages.download_app') }}</span>
                        @if(!empty($appVersion))
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-2 py-0.5">v{{ $appVersion }}</span>
                        @endif
                    </a>
                @else
                    <button type="button" class="btn btn-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#appDownloadNoticeModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                        <span class="small fw-semibold">{{ __('common::messages.download_app') }}</span>
                        @if(!empty($appVersion))
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-2 py-0.5">v{{ $appVersion }}</span>
                        @endif
                    </button>
                @endif

                <!-- Web Login Button (Compact, App keyword removed) -->
                @php
                    $isAuth = auth('user_web')->check();
                    $targetRoute = $isAuth ? route('my-books') : route('select-type');
                    $targetText = $isAuth ? __('common::messages.my_books') : __('common::messages.login_web');
                @endphp
                <a href="{{ $targetRoute }}" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <span class="small fw-semibold">{{ $targetText }}</span>
                    <svg class="chevron-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Right Column: Student Image -->
        <div class="col-12 col-lg-6 text-center text-lg-end">
            <div class="hero-student-wrapper d-inline-block shadow-lg rounded-4 ms-lg-auto">
                <img src="{{ web_asset('images/student.jpg') }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="img-fluid rounded-4 hero-student-img" loading="eager">
            </div>
        </div>
    </div>
</div>

@if(empty($appPath))
<!-- Modal when Desktop App is not uploaded yet in database -->
<div class="modal fade" id="appDownloadNoticeModal" tabindex="-1" aria-labelledby="appDownloadNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content tenant-modal rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 px-3 px-sm-4 pb-4">
                <div class="mb-3 text-primary d-inline-flex align-items-center justify-content-center bg-primary-subtle rounded-circle p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <h2 class="h5 fw-bold mb-2" id="appDownloadNoticeModalLabel">
                    {{ __('common::messages.coming_soon') }}
                </h2>
                <p class="text-secondary mb-4 small">
                    {{ __('common::messages.app_notice') }}
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                    <a href="{{ $targetRoute }}" class="btn btn-primary rounded-pill px-4 py-2 fw-medium">
                        {{ __('common::messages.continue_web') }}
                    </a>
                    <button type="button" class="btn btn-white border rounded-pill px-4 py-2" data-bs-dismiss="modal">
                        {{ __('common::messages.go_back') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

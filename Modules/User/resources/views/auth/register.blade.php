@extends('common::web.layouts.master')

@php
    $type = session('user_type', 'student');
    $typeLabel = $type === 'teacher' ? __('user::message.teacher') : __('user::message.student');
    $tenantName = tenant()?->name ?? config('app.name');
    $initialStep = 1;
    if ($errors->has('password') || $errors->has('password_confirmation')) {
        $initialStep = 4;
    } elseif ($errors->has('code')) {
        $initialStep = 3;
    } elseif ($errors->has('country_id') || $errors->has('city_id') || $errors->has('region_id') || $errors->has('school_id')) {
        $initialStep = 2;
    }
@endphp

@section('title', __('user::message.create_account'))
@section('main_class', 'vh-100 w-100 p-0 position-relative')

@section('content')
<div class="container-fluid h-100 p-3 p-md-4 p-xl-4">
    <div class="row h-100 g-0 gx-4 gx-xl-5 align-items-stretch">
        <!-- Left: Register Form Wizard -->
        <div class="col-12 col-lg-5 col-xl-5 d-flex flex-column justify-content-center h-100 px-3 px-md-4 px-xl-5 py-3">
            <div class="w-100 wizard-form-container mx-auto my-auto py-2">
                <!-- Header with Back Button and Title -->
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('welcome') }}" class="btn btn-white bg-white text-dark rounded-circle shadow-sm p-0 d-inline-flex align-items-center justify-content-center back-btn me-3 flex-shrink-0" aria-label="{{ __('user::message.back') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </a>
                    <h1 class="fw-bold fs-4 text-dark mb-0">
                        {{ __('user::message.register_title', ['type' => $typeLabel]) }}
                    </h1>
                </div>

                <!-- 5-segment Progress Indicator -->
                <div class="row g-2 mb-4 wizard-progress">
                    <div class="col"><div class="wizard-progress-bar active" data-step="1"></div></div>
                    <div class="col"><div class="wizard-progress-bar" data-step="2"></div></div>
                    <div class="col"><div class="wizard-progress-bar" data-step="3"></div></div>
                    <div class="col"><div class="wizard-progress-bar" data-step="4"></div></div>
                    <div class="col"><div class="wizard-progress-bar" data-step="5"></div></div>
                </div>

                <!-- Hidden Nav Tabs for Bootstrap Tab Controller -->
                <ul class="nav nav-tabs d-none" id="wizardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="step-1-tab" data-bs-toggle="tab" data-bs-target="#step-1" type="button" role="tab">1</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="step-2-tab" data-bs-toggle="tab" data-bs-target="#step-2" type="button" role="tab">2</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="step-3-tab" data-bs-toggle="tab" data-bs-target="#step-3" type="button" role="tab">3</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="step-4-tab" data-bs-toggle="tab" data-bs-target="#step-4" type="button" role="tab">4</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="step-5-tab" data-bs-toggle="tab" data-bs-target="#step-5" type="button" role="tab">5</button>
                    </li>
                </ul>

                <!-- Form Wrapping All Wizard Steps -->
                <form id="registerForm" action="{{ route('register.submit') }}" method="POST" autocomplete="on" data-loader>
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="tab-content" id="wizardTabContent">
                        <!-- Step 1: Basic Details -->
                        <div class="tab-pane fade show active" id="step-1" role="tabpanel">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.full_name') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </span>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3 @error('name') is-invalid @enderror"
                                        placeholder="{{ __('user::message.enter_name') }}" required autofocus>
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="name-error"></div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.username') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="4"></circle>
                                            <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                                        class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3 @error('username') is-invalid @enderror"
                                        placeholder="{{ __('user::message.enter_username') }}" required>
                                </div>
                                <div class="form-text text-secondary small mt-1">
                                    {{ __('user::message.username_hint') }}
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="username-error"></div>
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.email') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                    </span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3 @error('email') is-invalid @enderror"
                                        placeholder="{{ __('user::message.enter_email') }}" required>
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="email-error"></div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Continue Button -->
                            <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mb-4 btn-next" data-next="2">
                                {{ __('user::message.continue') }}
                            </button>

                            <!-- Sign In Link -->
                            <div class="text-center small text-secondary">
                                {{ __('user::message.have_account') }}
                                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                                    {{ __('user::message.sign_in') }}
                                </a>
                            </div>
                        </div>

                        <!-- Step 2: Location & School -->
                        <div class="tab-pane fade" id="step-2" role="tabpanel">
                            <!-- Country -->
                            <div class="mb-3">
                                <label for="country_id" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.country') }}
                                </label>
                                <select id="country_id" name="country_id" class="form-select bg-white rounded-3 py-2 px-3 @error('country_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('country_id') ? '' : 'selected' }}>
                                        {{ __('user::message.select_country') }}
                                    </option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->getTranslation('title', 'en') }} - {{ $country->getTranslation('title', 'ar') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger small mt-1 step-error d-none" id="country_id-error"></div>
                                @error('country_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- City -->
                            <div class="mb-3">
                                <label for="city_id" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.city') }}
                                </label>
                                <select id="city_id" name="city_id" class="form-select bg-white rounded-3 py-2 px-3 @error('city_id') is-invalid @enderror" {{ old('city_id') ? '' : 'disabled' }} required>
                                    <option value="" disabled {{ old('city_id') ? '' : 'selected' }}>
                                        {{ __('user::message.select_city') }}
                                    </option>
                                </select>
                                <div class="text-danger small mt-1 step-error d-none" id="city_id-error"></div>
                                @error('city_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Zone / Region -->
                            <div class="mb-3">
                                <label for="region_id" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.zone') }}
                                </label>
                                <select id="region_id" name="region_id" class="form-select bg-white rounded-3 py-2 px-3 @error('region_id') is-invalid @enderror" {{ old('region_id') ? '' : 'disabled' }} required>
                                    <option value="" disabled {{ old('region_id') ? '' : 'selected' }}>
                                        {{ __('user::message.select_zone') }}
                                    </option>
                                </select>
                                <div class="text-danger small mt-1 step-error d-none" id="region_id-error"></div>
                                @error('region_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- School -->
                            <div class="mb-4">
                                <label for="school_id" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.school') }}
                                </label>
                                <select id="school_id" name="school_id" class="form-select bg-white rounded-3 py-2 px-3 @error('school_id') is-invalid @enderror" {{ old('school_id') ? '' : 'disabled' }} required>
                                    <option value="" disabled {{ old('school_id') ? '' : 'selected' }}>
                                        {{ __('user::message.select_school') }}
                                    </option>
                                </select>
                                <div class="text-danger small mt-1 step-error d-none" id="school_id-error"></div>
                                @error('school_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold btn-prev" data-prev="1">
                                        {{ __('user::message.back') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold btn-next" data-next="3">
                                        {{ __('user::message.continue') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Access Code -->
                        <div class="tab-pane fade" id="step-3" role="tabpanel">
                            <!-- Book Access Code -->
                            <div class="mb-4">
                                <label for="code" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.access_code') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="7.5" cy="15.5" r="5.5"></circle>
                                            <path d="M21 2l-9.6 9.6"></path>
                                            <path d="m15.5 7.5 3 3"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="code" name="code" value="{{ old('code') }}"
                                        class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3 @error('code') is-invalid @enderror"
                                        placeholder="{{ __('user::message.enter_code') }}" required>
                                </div>
                                <div class="form-text text-secondary small mt-1">
                                    {{ __('user::message.code_hint') }}
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="code-error"></div>
                                @error('code')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold btn-prev" data-prev="2">
                                        {{ __('user::message.back') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold btn-next" data-next="4">
                                        {{ __('user::message.continue') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Password -->
                        <div class="tab-pane fade" id="step-4" role="tabpanel">
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.password') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    </span>
                                    <input type="password" id="password" name="password"
                                        class="form-control bg-white border-start-0 border-end-0 py-2 @error('password') is-invalid @enderror"
                                        placeholder="{{ __('user::message.create_password') }}" required minlength="8">
                                    <button type="button" class="input-group-text bg-white border-start-0 rounded-end-3 text-secondary pe-3 toggle-pwd" data-target="#password" aria-label="Toggle password visibility">
                                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg class="eye-slash-icon d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>
                                    </button>
                                </div>
                                <div class="form-text text-secondary small mt-1">
                                    {{ __('user::message.password_hint') }}
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="password-error"></div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label small text-secondary fw-semibold">
                                    {{ __('user::message.confirm_password') }}
                                </label>
                                <div class="input-group rounded-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    </span>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control bg-white border-start-0 border-end-0 py-2"
                                        placeholder="{{ __('user::message.reenter_password') }}" required minlength="8">
                                    <button type="button" class="input-group-text bg-white border-start-0 rounded-end-3 text-secondary pe-3 toggle-pwd" data-target="#password_confirmation" aria-label="Toggle password visibility">
                                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg class="eye-slash-icon d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-danger small mt-1 step-error d-none" id="password_confirmation-error"></div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold btn-prev" data-prev="3">
                                        {{ __('user::message.back') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold btn-next" data-next="5">
                                        {{ __('user::message.continue') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Review & Submit -->
                        <div class="tab-pane fade" id="step-5" role="tabpanel">
                            <p class="text-secondary small mb-3">
                                {{ __('user::message.review_desc') }}
                            </p>

                            <!-- Summary Card -->
                            <div class="card border-0 bg-white rounded-4 shadow-sm p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-secondary small">{{ __('user::message.account_type') }}</span>
                                    <span class="fw-semibold text-dark">{{ $typeLabel }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-secondary small">{{ __('user::message.full_name') }}</span>
                                    <span class="fw-semibold text-dark" id="review-name">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-secondary small">{{ __('user::message.username') }}</span>
                                    <span class="fw-semibold text-dark" id="review-username">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-secondary small">{{ __('user::message.email') }}</span>
                                    <span class="fw-semibold text-dark" id="review-email">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-secondary small">{{ __('user::message.school') }}</span>
                                    <span class="fw-semibold text-dark text-truncate ms-2" id="review-school">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small">{{ __('user::message.access_code') }}</span>
                                    <span class="fw-semibold text-dark" id="review-code">-</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold btn-prev" data-prev="4">
                                        {{ __('user::message.back') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold" id="btn-submit">
                                        {{ __('user::message.register_btn') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Illustration Banner -->
        <div class="col-12 col-lg-7 col-xl-7 d-none d-lg-block h-100 py-2 py-xl-3 pe-xl-2">
            <div class="h-100 position-relative w-100">
                <img src="{{ web_asset('images/register.webp') ?: web_asset('images/login.png') }}" alt="{{ $tenantName }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-5 shadow-sm" loading="eager">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(function () {
        const initialStep = {{ $initialStep }};
        const oldCityId = @json(old('city_id'));
        const oldRegionId = @json(old('region_id'));
        const oldSchoolId = @json(old('school_id'));

        // Wizard Step Navigation
        function goToStep(step) {
            // Update progress bars
            $('.wizard-progress-bar').each(function () {
                const barStep = parseInt($(this).data('step'), 10);
                $(this).toggleClass('active', barStep <= step);
            });

            // If navigating to review step (5), populate summary
            if (step === 5) {
                $('#review-name').text($('#name').val().trim() || '-');
                $('#review-username').text($('#username').val().trim() || '-');
                $('#review-email').text($('#email').val().trim() || '-');
                const schoolText = $('#school_id option:selected').text();
                $('#review-school').text($('#school_id').val() ? schoolText.trim() : '-');
                $('#review-code').text($('#code').val().trim() || '-');
            }

            // Trigger Bootstrap Tab
            const tabTrigger = document.querySelector('#step-' + step + '-tab');
            if (tabTrigger) {
                const tab = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                tab.show();
            }
        }

        // Validate individual steps
        function validateStep(currentStep) {
            let isValid = true;
            $('.step-error').addClass('d-none').text('');
            $('input, select').removeClass('is-invalid');
            $('.input-group').removeClass('is-invalid');

            function markInvalid($el, $err, msg) {
                $el.addClass('is-invalid');
                $el.closest('.input-group').addClass('is-invalid');
                $err.removeClass('d-none').text(msg);
                isValid = false;
            }

            if (currentStep === 1) {
                const name = $('#name').val().trim();
                const username = $('#username').val().trim();
                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!name) {
                    markInvalid($('#name'), $('#name-error'), @json(__('validation.required', ['attribute' => __('user::message.full_name')])));
                }
                if (!username) {
                    markInvalid($('#username'), $('#username-error'), @json(__('validation.required', ['attribute' => __('user::message.username')])));
                }
                if (!email) {
                    markInvalid($('#email'), $('#email-error'), @json(__('validation.required', ['attribute' => __('user::message.email')])));
                } else if (!emailRegex.test(email)) {
                    markInvalid($('#email'), $('#email-error'), @json(__('validation.email', ['attribute' => __('user::message.email')])));
                }
            } else if (currentStep === 2) {
                const country = $('#country_id').val();
                const city = $('#city_id').val();
                const region = $('#region_id').val();
                const school = $('#school_id').val();

                if (!country) {
                    markInvalid($('#country_id'), $('#country_id-error'), @json(__('validation.required', ['attribute' => __('user::message.country')])));
                }
                if (!city) {
                    markInvalid($('#city_id'), $('#city_id-error'), @json(__('validation.required', ['attribute' => __('user::message.city')])));
                }
                if (!region) {
                    markInvalid($('#region_id'), $('#region_id-error'), @json(__('validation.required', ['attribute' => __('user::message.zone')])));
                }
                if (!school) {
                    markInvalid($('#school_id'), $('#school_id-error'), @json(__('validation.required', ['attribute' => __('user::message.school')])));
                }
            } else if (currentStep === 3) {
                const code = $('#code').val().trim();
                if (!code) {
                    markInvalid($('#code'), $('#code-error'), @json(__('validation.required', ['attribute' => __('user::message.access_code')])));
                }
            } else if (currentStep === 4) {
                const pwd = $('#password').val();
                const confirmPwd = $('#password_confirmation').val();

                if (!pwd || pwd.length < 8) {
                    markInvalid($('#password'), $('#password-error'), @json(__('validation.min.string', ['attribute' => __('user::message.password'), 'min' => 8])));
                }
                if (pwd !== confirmPwd) {
                    markInvalid($('#password_confirmation'), $('#password_confirmation-error'), @json(__('user::message.password_mismatch')));
                }
            }

            return isValid;
        }

        // Next / Prev Button Clicks
        $(document).on('click', '.btn-next', function () {
            const nextStep = parseInt($(this).data('next'), 10);
            const currentStep = nextStep - 1;
            if (validateStep(currentStep)) {
                goToStep(nextStep);
            }
        });

        $(document).on('click', '.btn-prev', function () {
            const prevStep = parseInt($(this).data('prev'), 10);
            goToStep(prevStep);
        });

        // Cascading AJAX Location Dropdowns
        $('#country_id').on('change', function () {
            const countryId = $(this).val();
            $('#city_id').html('<option value="" disabled selected>{{ __('user::message.select_city') }}</option>').prop('disabled', true);
            $('#region_id').html('<option value="" disabled selected>{{ __('user::message.select_zone') }}</option>').prop('disabled', true);
            $('#school_id').html('<option value="" disabled selected>{{ __('user::message.select_school') }}</option>').prop('disabled', true);

            if (countryId) {
                $.get('{{ route("web.ajax.cities") }}', { country_id: countryId }, function (html) {
                    $('#city_id').html(html).prop('disabled', false);
                    if (oldCityId) {
                        $('#city_id').val(oldCityId).trigger('change');
                    }
                });
            }
        });

        $('#city_id').on('change', function () {
            const cityId = $(this).val();
            $('#region_id').html('<option value="" disabled selected>{{ __('user::message.select_zone') }}</option>').prop('disabled', true);
            $('#school_id').html('<option value="" disabled selected>{{ __('user::message.select_school') }}</option>').prop('disabled', true);

            if (cityId) {
                $.get('{{ route("web.ajax.regions") }}', { city_id: cityId }, function (html) {
                    $('#region_id').html(html).prop('disabled', false);
                    if (oldRegionId) {
                        $('#region_id').val(oldRegionId).trigger('change');
                    }
                });
            }
        });

        $('#region_id').on('change', function () {
            const regionId = $(this).val();
            $('#school_id').html('<option value="" disabled selected>{{ __('user::message.select_school') }}</option>').prop('disabled', true);

            if (regionId) {
                $.get('{{ route("web.ajax.schools") }}', {
                    country_id: $('#country_id').val(),
                    city_id: $('#city_id').val(),
                    region_id: regionId
                }, function (html) {
                    $('#school_id').html(html).prop('disabled', false);
                    if (oldSchoolId) {
                        $('#school_id').val(oldSchoolId);
                    }
                });
            }
        });

        // Initialize cascading if old input exists
        if ($('#country_id').val()) {
            $('#country_id').trigger('change');
        }

        // Initialize Step if server validation sent us to a specific step
        if (initialStep > 1) {
            goToStep(initialStep);
        }
    });
</script>
@endsection

@extends('common::web.layouts.master')

@php
    $tenantName = tenant()?->name ?? config('app.name');
@endphp

@section('title', __('user::message.reset_title'))
@section('main_class', 'vh-100 w-100 p-0 position-relative')

@section('content')
<!-- Back Button -->
<div class="position-absolute top-0 start-0 m-3 m-lg-4 z-3">
    <a href="{{ route('login') }}" class="btn btn-white bg-white text-dark rounded-circle shadow-sm p-0 d-inline-flex align-items-center justify-content-center back-btn" aria-label="{{ __('user::message.back') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>
</div>

<div class="container-fluid h-100 p-3 p-md-4 p-xl-4">
    <div class="row h-100 g-0 gx-4 gx-xl-5 align-items-stretch">
        <!-- Left: Reset Password Form -->
        <div class="col-12 col-lg-5 col-xl-5 d-flex flex-column justify-content-center h-100 px-3 px-md-4 px-xl-5 py-3">
            <div class="w-100 auth-form-container mx-auto my-auto py-2">
                <!-- Logo -->
                <div class="mb-3 text-start">
                    <img src="{{ web_asset('images/logo.webp') }}" alt="{{ $tenantName }}" class="img-fluid auth-logo">
                </div>

                <!-- Headline -->
                <h1 class="fw-bold fs-2 text-dark mb-1">
                    {{ __('user::message.reset_title') }}
                </h1>
                <p class="text-secondary small mb-4">
                    {{ __('user::message.reset_desc') }}
                </p>

                <!-- Form -->
                <form id="resetPasswordForm" action="{{ route('password.update') }}" method="POST" autocomplete="off" data-ajax>
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label small text-secondary fw-semibold">
                            {{ __('user::message.new_password') }}
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
                                placeholder="{{ __('user::message.enter_password') }}" required minlength="8" autofocus>
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
                        <div id="password-error" class="text-danger small mt-1 step-error @error('password') @else d-none @enderror">
                            @error('password') {{ $message }} @enderror
                        </div>
                        <div class="form-text text-secondary small mt-1">
                            {{ __('user::message.password_hint') }}
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label small text-secondary fw-semibold">
                            {{ __('user::message.confirm_new') }}
                        </label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control bg-white border-start-0 border-end-0 py-2 @error('password_confirmation') is-invalid @enderror"
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
                        <div id="password_confirmation-error" class="text-danger small mt-1 step-error @error('password_confirmation') @else d-none @enderror">
                            @error('password_confirmation') {{ $message }} @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mb-4" id="btn-submit">
                        {{ __('user::message.reset_btn') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Illustration Banner -->
        <div class="col-12 col-lg-7 col-xl-7 d-none d-lg-block h-100 py-2 py-xl-3 pe-xl-2">
            <div class="h-100 position-relative w-100">
                <img src="{{ web_asset('images/otp.webp') }}" alt="{{ $tenantName }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-5 shadow-sm" loading="eager">
            </div>
        </div>
    </div>
</div>
@endsection


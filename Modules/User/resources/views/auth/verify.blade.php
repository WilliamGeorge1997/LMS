@extends('common::web.layouts.master')

@php
    $tenantName = tenant()?->name ?? config('app.name');
@endphp

@section('title', __('user::message.verify_title'))
@section('main_class', 'vh-100 w-100 p-0 position-relative')

@section('content')
<!-- Back Button -->
<div class="position-absolute top-0 start-0 m-3 m-lg-4 z-3">
    <a href="{{ route('welcome') }}" class="btn btn-white bg-white text-dark rounded-circle shadow-sm p-0 d-inline-flex align-items-center justify-content-center back-btn" aria-label="{{ __('user::message.back') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>
</div>

<div class="container-fluid h-100 p-3 p-md-4 p-xl-4">
    <div class="row h-100 g-0 gx-4 gx-xl-5 align-items-stretch">
        <!-- Left: OTP Form -->
        <div class="col-12 col-lg-5 col-xl-5 d-flex flex-column justify-content-center h-100 px-3 px-md-4 px-xl-5 py-3">
            <div class="w-100 auth-form-container mx-auto my-auto py-2">
                <!-- Logo -->
                <div class="mb-3 text-start">
                    <img src="{{ web_asset('images/logo.webp') }}" alt="{{ $tenantName }}" class="img-fluid auth-logo">
                </div>

                <!-- Headline -->
                <h1 class="fw-bold fs-2 text-dark mb-1">
                    {{ __('user::message.verify_title') }}
                </h1>
                <p class="text-secondary small mb-4">
                    {{ __('user::message.verify_desc', ['email' => $email]) }}
                </p>

                <!-- Status Messages -->
                <div id="resendAlert" class="text-success small mb-3 d-none"></div>

                @if (session('success'))
                    <div class="text-success small mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- OTP Form -->
                <form id="otpForm" action="{{ route('verify.submit') }}" method="POST" autocomplete="off" data-ajax>
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="otp" id="otp-hidden" value="">

                    <!-- 6 OTP Input Boxes -->
                    <div class="d-flex justify-content-between gap-2 mb-2 otp-container">
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="0" autofocus required>
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="1" required>
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="2" required>
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="3" required>
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="4" required>
                        <input type="text" class="form-control otp-box text-center fw-bold fs-4 bg-white rounded-3 shadow-sm" maxlength="1" pattern="[0-9]*" inputmode="numeric" data-index="5" required>
                    </div>
                    <div id="otp-error" class="text-danger small mb-3 step-error @error('otp') @else d-none @enderror">
                        @error('otp') {{ $message }} @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mb-4" id="btn-verify">
                        {{ __('user::message.verify_btn') }}
                    </button>

                    <!-- Resend Section -->
                    <div class="text-center small text-secondary">
                        {{ __('user::message.resend_prompt') }}
                        <button type="button" class="btn btn-link p-0 text-primary text-decoration-none fw-semibold small"
                            id="resendBtn"
                            data-resend="{{ route('verify.resend') }}"
                            data-resend-cooldown="60"
                            data-resend-alert="resendAlert">
                            <span class="resend-text">{{ __('user::message.resend_code') }}</span>
                            <span class="resend-countdown d-none"></span>
                        </button>
                    </div>
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


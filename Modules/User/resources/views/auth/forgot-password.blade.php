@extends('common::web.layouts.master')

@php
    $tenantName = tenant()?->name ?? config('app.name');
@endphp

@section('title', __('user::message.forgot_title'))
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
        <!-- Left: Forgot Password Form -->
        <div class="col-12 col-lg-5 col-xl-5 d-flex flex-column justify-content-center h-100 px-3 px-md-4 px-xl-5 py-3">
            <div class="w-100 auth-form-container mx-auto my-auto py-2">
                <!-- Logo -->
                <div class="mb-3 text-start">
                    <img src="{{ web_asset('images/logo.png') }}" alt="{{ $tenantName }}" class="img-fluid auth-logo">
                </div>

                <!-- Headline -->
                <h1 class="fw-bold fs-2 text-dark mb-1">
                    {{ __('user::message.forgot_title') }}
                </h1>
                <p class="text-secondary small mb-4">
                    {{ __('user::message.forgot_desc') }}
                </p>

                @if (session('status'))
                    <div class="text-success small mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form -->
                <form id="forgotForm" action="{{ route('password.email') }}" method="POST" autocomplete="on" data-ajax>
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label small text-secondary fw-semibold">
                            {{ __('user::attributes.email') }}
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
                                placeholder="you@example.com" required autofocus>
                        </div>
                        <div id="email-error" class="text-danger small mt-1 step-error @error('email') @else d-none @enderror">
                            @error('email') {{ $message }} @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mb-4" id="btn-submit">
                        {{ __('user::message.send_code') }}
                    </button>

                    <!-- Back to Sign in -->
                    <div class="text-center small text-secondary">
                        {{ __('user::message.remember_password') }}
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                            {{ __('user::message.sign_in') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Illustration Banner -->
        <div class="col-12 col-lg-7 col-xl-7 d-none d-lg-block h-100 py-2 py-xl-3 pe-xl-2">
            <div class="h-100 position-relative w-100">
                <img src="{{ web_asset('images/register.png') ?: web_asset('images/login.png') }}" alt="{{ $tenantName }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-5 shadow-sm" loading="eager">
            </div>
        </div>
    </div>
</div>
@endsection


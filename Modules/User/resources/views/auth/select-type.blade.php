@extends('common::web.layouts.master')

@section('title', __('user::message.select_type'))
@section('main_class', 'min-vh-100 d-flex flex-column justify-content-center align-items-center py-4 px-3 position-relative')

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

<div class="container auth-container">
    <!-- Logo -->
    <div class="text-center mb-4">
        <img src="{{ web_asset('images/logo.webp') }}" alt="{{ config('app.name') }}" class="img-fluid mx-auto auth-logo">
    </div>

    <!-- Headline -->
    <h1 class="text-center fw-bold mb-4 mb-md-5 fs-2">
        {{ __('user::message.select_type') }}
    </h1>

    <!-- Account Type Cards -->
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-sm-6">
            <x-user::type-card
                :title="__('user::message.student')"
                :image="web_asset('images/student.jpg')"
                :button-text="__('user::message.continue_student')"
                type="student"
            />
        </div>

        <div class="col-12 col-sm-6">
            <x-user::type-card
                :title="__('user::message.teacher')"
                :image="web_asset('images/teacher.jpg')"
                :button-text="__('user::message.continue_teacher')"
                type="teacher"
            />
        </div>
    </div>
</div>
@endsection

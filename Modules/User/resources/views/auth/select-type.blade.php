@extends('common::web.layouts.master')

@section('title', __('user::message.select_type'))

@section('content')
<div class="container auth-container">
    <!-- Logo -->
    <div class="text-center mb-4">
        <img src="{{ web_asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid mx-auto auth-logo">
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

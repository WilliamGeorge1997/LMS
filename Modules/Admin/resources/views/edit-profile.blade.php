@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('admin::attributes.edit_profile'))

@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ __('admin::attributes.edit_profile') }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ url('/admin/dashboard') }}" class="text-muted text-hover-primary">
                        {{ __('common::sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">{{ __('admin::attributes.edit_profile') }}</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 py-4">
            <h3 class="card-title fw-bold fs-5 m-0">{{ __('admin::attributes.edit_profile') }}</h3>
        </div>
        <div class="card-body pt-0">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-4 mb-6">
                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-3"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-column">
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-6">
                    <div class="col-12 text-center mb-4">
                        <div class="symbol symbol-100px symbol-circle mb-3">
                            <img src="{{ $admin?->image ?? asset('dashboard/assets/media/avatars/blank.png') }}"
                                class="rounded-circle object-fit-cover w-100px h-100px" alt="{{ $admin->name }}" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <label for="profile-image" class="btn btn-sm btn-light-primary">
                                <i class="ki-duotone ki-pencil fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('admin::attributes.image') }}
                            </label>
                            <input id="profile-image" type="file" name="image" class="d-none"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                        </div>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="profile-name" class="required form-label">{{ __('admin::attributes.name') }}</label>
                        <input id="profile-name" type="text" name="name"
                            class="form-control form-control-solid @error('name') is-invalid @enderror"
                            value="{{ old('name', $admin->name) }}" placeholder="{{ __('admin::attributes.enter_name') }}" autocomplete="off" />
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="profile-email" class="required form-label">{{ __('admin::attributes.email') }}</label>
                        <input id="profile-email" type="email" name="email"
                            class="form-control form-control-solid @error('email') is-invalid @enderror"
                            value="{{ old('email', $admin->email) }}" placeholder="{{ __('admin::attributes.enter_email') }}" autocomplete="off" />
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="profile-password" class="form-label">{{ __('admin::attributes.password') }}</label>
                        <input id="profile-password" type="password" name="password"
                            class="form-control form-control-solid @error('password') is-invalid @enderror"
                            placeholder="{{ __('admin::attributes.leave_blank_to_keep_current') }}" autocomplete="new-password" />
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="profile-password-confirmation" class="form-label">{{ __('admin::attributes.confirm_password') }}</label>
                        <input id="profile-password-confirmation" type="password" name="password_confirmation"
                            class="form-control form-control-solid @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ __('admin::attributes.enter_confirm_password') }}" autocomplete="new-password" />
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-8">
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-light">
                        {{ __('admin::attributes.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('admin::attributes.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

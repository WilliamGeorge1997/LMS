@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('common::attributes.settings'))

@section('toolbar')
    <div id="create-toolbar-area">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('common::attributes.settings') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ url('/admin/settings') }}" class="text-muted text-hover-primary">{{ __('common::attributes.settings') }}</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __('common::attributes.settings') }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 py-4">
            <h3 class="card-title fw-bold fs-5 m-0">{{ __('common::attributes.settings') }}</h3>
        </div>

        <div class="card-body pt-0">
            <form id="create-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-6">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('common::attributes.app_version') }}</label>
                        <input type="text" name="app_version" class="form-control form-control-solid"
                            value="{{ $setting?->app_version }}" placeholder="1.0.0" autocomplete="off" />
                        <div class="invalid-feedback d-block" data-error="app_version"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('common::attributes.app_path') }}</label>
                        <input type="file" name="app_path" class="form-control form-control-solid" accept=".apk,.ipa" />
                        @if ($setting?->app_path)
                            <div class="mt-2">
                                <a href="{{ $setting->app_path }}" target="_blank" class="text-primary fs-7">
                                    {{ __('common::attributes.download_current_app') ?? 'Download Current App' }}
                                </a>
                            </div>
                        @endif
                        <div class="invalid-feedback d-block" data-error="app_path"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-8">
                    <button type="submit" id="create-submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('common::attributes.save_settings') }}</span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('dashboard/assets/js/custom/plugins/actions.js') }}"></script>
    <script>
        "use strict";
        
        Actions.initForm('#create-form', '#create-submit');
    </script>
@endsection

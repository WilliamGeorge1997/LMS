@extends('common::web.layouts.master')

@section('title', $title ?? __('common::messages.not_found'))

@section('content')
<div class="container text-center my-auto auth-box-sm">
    <!-- Logo -->
    <div class="mb-4">
        <img src="{{ web_asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid mx-auto auth-logo">
    </div>

    <!-- Card Container -->
    <div class="bg-white rounded-4 border p-4 p-sm-5 shadow-sm">
        <!-- Icon Badge -->
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-primary mb-4 badge-icon-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="h3 fw-bold mb-3">
            {{ $title }}
        </h1>

        <!-- Description -->
        <p class="text-muted small mb-4 lh-base">
            {{ $description }}
        </p>

        <!-- Info Box -->
        <div class="bg-light border rounded-3 p-3 text-start mb-4 small text-muted">
            <div class="fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#7A9B57" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('common::messages.access_help') }}
            </div>
            <p class="mb-1">
                {!! __('common::messages.url_hint', ['host' => '<code class="text-danger fw-semibold">school-name.' . request()->getHost() . '</code>']) !!}
            </p>
            <p class="mb-0 text-muted small">
                {{ __('common::messages.contact_admin') }}
            </p>
        </div>

        <!-- Action Button -->
        <a href="javascript:history.back()" class="btn btn-primary rounded-pill w-100 fw-semibold py-2">
            {{ __('common::messages.go_back') }}
        </a>
    </div>
</div>
@endsection

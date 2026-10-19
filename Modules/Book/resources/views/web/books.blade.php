@extends('common::web.layouts.app')

@section('title', __('book::attributes.my_books'))

@section('app_content')
<div class="container-fluid px-0">
    <!-- Header: Welcome & Add Code Button -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 mb-md-5">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">
                {{ __('book::messages.welcome_user', ['name' => $user->name]) }}
            </h1>
            <p class="text-secondary mb-0">
                {{ __('book::messages.welcome_back') }}
            </p>
        </div>

        <div>
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-medium d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAccessCodeModal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>{{ __('book::buttons.add_code') }}</span>
            </button>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div id="sessionSuccessAlert" class="alert alert-success fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-inline-flex flex-shrink-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="fw-medium text-success-emphasis">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close position-static p-2 my-auto ms-auto shadow-none" data-bs-dismiss="alert" aria-label="{{ __('book::buttons.cancel') }}"></button>
        </div>
    @endif

    <!-- My Books Section -->
    <section class="mb-4">
        <h2 class="h4 fw-bold text-dark mb-4">{{ __('book::attributes.my_books') }}</h2>

        @if($books->isNotEmpty())
            <!-- Books Grid -->
            <div class="d-flex flex-wrap gap-4 mb-4">
                @foreach($books as $book)
                    <div class="card book-card border-0 rounded-4 shadow-sm p-3">
                        <!-- Book Cover -->
                        <div class="rounded-3 mb-3 position-relative ratio ratio-4x5 book-cover-placeholder">
                            @if($book->cover)
                                <img src="{{ $book->cover }}" alt="{{ $book->title }}" 
                                     class="object-fit-cover w-100 h-100 rounded-3 book-cover-img"
                                     onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                                     loading="lazy">
                            @endif
                            
                            <!-- Green Placeholder matching design mockup (#275a4e) -->
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 w-100 h-100 rounded-3 {{ $book->cover ? 'd-none' : '' }}">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="opacity-90 mb-2">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-0-5H20"></path>
                                    <path d="M6 6h10"></path>
                                    <path d="M6 10h10"></path>
                                </svg>
                                <span class="text-white small fw-medium text-center px-1 opacity-90">{{ $book->title }}</span>
                            </div>
                        </div>

                        <!-- Book Info -->
                        <div class="d-flex flex-column flex-grow-1 justify-content-between">
                            <div>
                                <!-- Title -->
                                <h3 class="h6 fw-bold mb-2 text-truncate book-title" title="{{ $book->title }}">
                                    {{ $book->title }}
                                </h3>

                                <!-- Badges: Category & Level under title -->
                                @if($book->category?->title || $book->level?->title)
                                    <div class="d-flex align-items-center gap-1 mb-3 flex-wrap">
                                        @if($book->category?->title)
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle small fw-medium">
                                                {{ $book->category->title }}
                                            </span>
                                        @endif
                                        @if($book->level?->title)
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle small fw-medium">
                                                {{ $book->level->title }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="mb-3"></div>
                                @endif
                            </div>

                            <!-- Action: Open in new tab (Full Width, Identity Color) -->
                            <div>
                                @if($book->path)
                                    <a href="{{ $book->path }}" target="_blank" rel="noopener noreferrer" 
                                       class="btn btn-primary rounded-pill w-100 py-2 fw-medium d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none shadow-sm">
                                        <span>{{ __('book::buttons.open') }}</span>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                @else
                                    <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-medium d-inline-flex align-items-center justify-content-center gap-2 text-white disabled opacity-75" disabled>
                                        <span>{{ __('book::buttons.open') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
            <div class="card border-0 rounded-4 shadow-sm p-5 text-center my-4">
                <div class="mb-3 text-secondary opacity-50">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-0-5H20"></path>
                        <path d="M6 6h10"></path>
                        <path d="M6 10h10"></path>
                    </svg>
                </div>
                <h3 class="h5 fw-bold text-dark mb-2">{{ __('book::messages.no_books') }}</h3>
                <p class="text-secondary small mb-4">{{ __('book::messages.enter_code') }}</p>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-medium d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAccessCodeModal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>{{ __('book::buttons.add_code') }}</span>
                    </button>
                </div>
            </div>
        @endif
    </section>
</div>

<!-- Add Access Code Modal -->
<div class="modal fade" id="addAccessCodeModal" tabindex="-1" aria-labelledby="addAccessCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3 p-sm-4 tenant-modal">
            <div class="modal-header border-0 pb-0 d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 bg-white p-2 d-inline-flex align-items-center justify-content-center shadow-sm border">
                        <img src="{{ web_asset('images/logo.webp') }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="sidebar-logo">
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-1" id="addAccessCodeModalLabel">
                            {{ __('book::buttons.add_code') }}
                        </h5>
                        <p class="text-secondary small mb-0">
                            {{ __('book::messages.enter_code') }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-white bg-white rounded-circle shadow-sm p-2 text-secondary d-flex align-items-center justify-content-center border" data-bs-dismiss="modal" aria-label="{{ __('book::buttons.cancel') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="claimCodeForm" action="{{ route('book-codes.redeem') }}" method="POST" data-ajax>
                @csrf
                <div class="modal-body py-4">
                    <label for="code" class="form-label small text-secondary fw-semibold mb-2">
                        {{ __('book::attributes.access_code') }}
                    </label>

                    <!-- Auth-style Input Group -->
                    <div class="input-group rounded-3 shadow-sm">
                        <span class="input-group-text bg-white border-end-0 rounded-start-3 text-secondary ps-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                                <path d="M13 5v2"></path>
                                <path d="M13 17v2"></path>
                                <path d="M13 11v2"></path>
                            </svg>
                        </span>
                        <input type="text" id="code" name="code" 
                               class="form-control bg-white border-start-0 rounded-end-3 py-2 pe-3 text-uppercase fw-semibold" 
                               placeholder="{{ __('book::placeholders.code') }}" 
                               maxlength="20" required autocomplete="off">
                    </div>
                    <div class="text-danger small mt-1 step-error d-none" id="code-error"></div>
                </div>

                <div class="modal-footer border-0 pt-0 px-0 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-white bg-white rounded-pill px-4 py-2 text-secondary fw-medium shadow-sm border" data-bs-dismiss="modal">
                        {{ __('book::buttons.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        {{ __('book::buttons.unlock') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('common::web.layouts.app')

@section('title', __('book::attributes.my_books'))

@section('app_content')
<div class="container-fluid px-0">
    <!-- Header: Welcome & Add Code Button -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 mb-md-5">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">
                Welcome, {{ $user->isTeacher() ? 'Teacher' : ($user->isStudent() ? 'Student' : $user->name) }}!
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

    <!-- My Books Section -->
    <section class="mb-4">
        <h2 class="h4 fw-bold text-dark mb-4">{{ __('book::attributes.my_books') }}</h2>

        @if($books->isNotEmpty())
            <!-- Carousel Container -->
            <div class="position-relative px-4">
                <!-- Prev Button -->
                <button type="button" class="btn btn-secondary rounded-circle shadow p-0 carousel-nav-btn position-absolute top-50 start-0 translate-middle-y z-3" id="booksPrevBtn" aria-label="{{ __('book::buttons.previous') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <!-- Books Track -->
                <div class="books-slider d-flex align-items-stretch gap-4 overflow-x-auto pb-3 px-4" id="booksSlider">
                    @foreach($books as $book)
                        <div class="book-card-item flex-shrink-0">
                            <div class="card card-hover h-100 border-0 rounded-4 shadow-sm p-3">
                                <!-- Book Cover -->
                                <div class="rounded-3 mb-3 position-relative ratio ratio-4x5">
                                    @if($book->cover)
                                        <img src="{{ $book->cover }}" alt="{{ $book->title }}" class="object-fit-cover w-100 h-100 rounded-3">
                                    @else
                                        <!-- Styled Cover matching design mockup -->
                                        <div class="book-cover-placeholder d-flex align-items-center justify-content-center w-100 h-100 rounded-3">
                                            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="opacity-75">
                                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Book Info -->
                                <div class="d-flex flex-column flex-grow-1 justify-content-between">
                                    <div>
                                        <h3 class="h6 fw-bold text-dark mb-1 text-truncate" title="{{ $book->title }}">{{ $book->title }}</h3>
                                        <p class="text-secondary small mb-3 text-truncate">{{ $book->publisher?->name ?? (tenant()?->name ?? 'Canterbury Books') }}</p>
                                    </div>

                                    <div>
                                        @if($book->download)
                                            <a href="{{ $book->download }}" class="btn btn-light rounded-pill px-3 py-1 btn-sm fw-medium d-inline-flex align-items-center gap-1" download>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                <span>{{ __('book::buttons.download') }}</span>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-light rounded-pill px-3 py-1 btn-sm fw-medium d-inline-flex align-items-center gap-1 disabled" disabled>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                <span>{{ __('book::buttons.download') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Next Button -->
                <button type="button" class="btn btn-secondary rounded-circle shadow p-0 carousel-nav-btn position-absolute top-50 end-0 translate-middle-y z-3" id="booksNextBtn" aria-label="{{ __('book::buttons.next') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        @else
            <!-- Empty State -->
            <div class="card border-0 rounded-4 shadow-sm p-5 text-center my-4 bg-white">
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
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addAccessCodeModalLabel">{{ __('book::buttons.add_code') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="claimCodeForm" action="{{ route('my-books.claim-code') }}" method="POST" data-ajax>
                @csrf
                <div class="modal-body pt-2 pb-4">
                    <p class="text-secondary small mb-3">{{ __('book::messages.enter_code') }}</p>
                    <div class="mb-2">
                        <input type="text" name="code" class="form-control form-control-lg rounded-3 text-uppercase text-center fw-bold" placeholder="e.g. XC9V2B" maxlength="20" required autocomplete="off">
                        <p class="text-danger small mt-1 mb-0 step-error d-none" id="code-error"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('book::buttons.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span>{{ __('book::buttons.unlock') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

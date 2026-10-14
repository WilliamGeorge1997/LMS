@php
    $logo = web_asset('images/logo.png');
    $currentRoute = request()->route()?->getName();
    $isHome = $currentRoute === 'welcome' || request()->is('/');
    $isMyBooks = $currentRoute === 'my-books' || request()->is('my-books*');
    $isProfile = $currentRoute === 'profile' || request()->is('profile*');
@endphp

<!-- Mobile Top Header (Small Screens) -->
<header class="d-lg-none bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between sticky-top w-100 z-3">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebarOffcanvas" aria-controls="mobileSidebarOffcanvas" aria-label="Toggle Navigation">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <a href="{{ route('my-books') }}" class="d-inline-block" data-nav>
            <img src="{{ $logo }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="sidebar-logo">
        </a>
    </div>

    <div class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 d-inline-flex align-items-center gap-1">
        <span class="status-dot rounded-circle bg-success d-inline-block"></span>
        <span class="fw-medium">{{ __('common::sidebar.online') }}</span>
    </div>
</header>

<!-- Mobile Offcanvas Drawer -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebarOffcanvas" aria-labelledby="mobileSidebarOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <img src="{{ $logo }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="sidebar-logo">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
        <div class="nav flex-column gap-2">
            <a href="{{ route('welcome') }}" data-nav class="nav-link rounded-3 p-3 d-flex align-items-center gap-3 sidebar-nav-link {{ $isHome ? 'active' : 'text-secondary' }}" data-bs-dismiss="offcanvas">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span class="fw-medium">{{ __('common::sidebar.home') }}</span>
            </a>

            <a href="{{ route('my-books') }}" data-nav class="nav-link rounded-3 p-3 d-flex align-items-center gap-3 sidebar-nav-link {{ $isMyBooks ? 'active' : 'text-secondary' }}" data-bs-dismiss="offcanvas">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span class="fw-medium">{{ __('common::sidebar.my_books') }}</span>
            </a>

            <a href="{{ Route::has('profile') ? route('profile') : '#' }}" data-nav class="nav-link rounded-3 p-3 d-flex align-items-center gap-3 sidebar-nav-link {{ $isProfile ? 'active' : 'text-secondary' }}" data-bs-dismiss="offcanvas">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="fw-medium">{{ __('common::sidebar.profile') }}</span>
            </a>
        </div>

        <div class="pt-3 border-top">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 small d-flex align-items-center justify-content-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>{{ __('user::message.logout') ?? 'Logout' }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Desktop Vertical Sidebar -->
<aside class="app-sidebar d-none d-lg-flex flex-column align-items-center py-4 bg-white border-end position-sticky top-0 vh-100 z-2" id="mainSidebar">
    <!-- Brand / Logo -->
    <a href="{{ route('my-books') }}" class="mb-3 d-inline-block text-center" data-nav aria-label="{{ tenant()?->name ?? config('app.name') }}">
        <img src="{{ $logo }}" alt="{{ tenant()?->name ?? config('app.name') }}" class="sidebar-logo">
    </a>

    <!-- Status Pill (Bootstrap 5 native badge) -->
    <div class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 mb-4 d-inline-flex align-items-center gap-1">
        <span class="status-dot rounded-circle bg-success d-inline-block"></span>
        <span class="fw-medium">{{ __('common::sidebar.online') }}</span>
    </div>

    <!-- Navigation Stack -->
    <nav class="nav flex-column align-items-center gap-3 w-100 px-2 my-auto" aria-label="Main Navigation">
        <!-- Home -->
        <a href="{{ route('welcome') }}" data-nav class="nav-link sidebar-nav-item rounded-4 d-flex flex-column align-items-center justify-content-center text-center {{ $isHome ? 'active' : 'text-secondary' }}" title="{{ __('common::sidebar.home') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="small fw-medium">{{ __('common::sidebar.home') }}</span>
        </a>

        <!-- My Books -->
        <a href="{{ route('my-books') }}" data-nav class="nav-link sidebar-nav-item rounded-4 d-flex flex-column align-items-center justify-content-center text-center {{ $isMyBooks ? 'active' : 'text-secondary' }}" title="{{ __('common::sidebar.my_books') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            <span class="small fw-medium">{{ __('common::sidebar.my_books') }}</span>
        </a>

        <!-- Profile -->
        <a href="{{ Route::has('profile') ? route('profile') : '#' }}" data-nav class="nav-link sidebar-nav-item rounded-4 d-flex flex-column align-items-center justify-content-center text-center {{ $isProfile ? 'active' : 'text-secondary' }}" title="{{ __('common::sidebar.profile') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span class="small fw-medium">{{ __('common::sidebar.profile') }}</span>
        </a>
    </nav>

    <!-- Bottom Actions / Logout -->
    <div class="mt-auto pt-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light rounded-circle p-2 text-secondary" title="{{ __('user::message.logout') ?? 'Logout' }}" aria-label="{{ __('user::message.logout') ?? 'Logout' }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>
</aside>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') }} - Login</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="LMS Admin Panel - Sign in to manage courses, users, and platform settings for the Learning Management System." />
    <meta name="keywords"
        content="LMS, Learning Management System, admin, login, education, courses, user management, e-learning, dashboard, Laravel" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('dashboard/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>

<body id="kt_body" class="app-blank">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <a href="index.html" class="d-block d-lg-none mx-auto py-20">
                <img alt="Logo" src="{{ asset('dashboard/assets/media/logos/default-dark.svg') }}"
                    class="theme-dark-show h-25px" />
            </a>
            <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                <div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
                    <div class="py-20">
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form"
                            data-kt-redirect-url="{{ url('/admin/dashboard') }}" action="{{ url('/admin/login') }}">
                            @csrf
                            <div class="card-body">
                                <div class="text-start mb-10">
                                    <h1 class="text-gray-900 mb-3 fs-3x" data-kt-translate="sign-in-title">Sign In</h1>
                                </div>
                                <div class="fv-row mb-8">
                                    <input type="text" placeholder="Email" name="email" autocomplete="off"
                                        data-kt-translate="sign-in-input-email" class="form-control form-control-solid"
                                         />
                                </div>
                                <div><p class="text-danger" data-email-error></p></div>
                                <div class="fv-row mb-7">
                                    <input type="password" placeholder="Password" name="password" autocomplete="off"
                                        data-kt-translate="sign-in-input-password"
                                        class="form-control form-control-solid" />
                                </div>
                                <div class="d-flex flex-stack">
                                    <button id="kt_sign_in_submit" class="btn btn-primary me-2 flex-shrink-0">
                                        <span class="indicator-label" data-kt-translate="sign-in-submit">Sign In</span>
                                        <span class="indicator-progress">
                                            <span data-kt-translate="general-progress">Please wait...</span>
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="d-none d-lg-flex flex-lg-row-fluid w-50 bgi-size-cover bgi-position-y-center bgi-position-x-start bgi-no-repeat"
                style="background-image: url({{ asset('dashboard/assets/media/auth/bg11.png') }})"></div>
        </div>
    </div>
    <script src="{{ asset('dashboard/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/authentication/sign-in/general.js') }}"></script>
    {{-- <script src="assets/js/custom/authentication/sign-in/i18n.js"></script> --}}
</body>

</html>

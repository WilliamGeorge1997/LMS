@if(app()->getLocale() === 'ar')
    <link href="{{ asset('dashboard/assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard/assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
@else
    <link href="{{ asset('dashboard/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dashboard/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
@endif

<link href="{{ asset('dashboard/assets/css/custom/custom.css') }}" rel="stylesheet" type="text/css" />

@yield('css')

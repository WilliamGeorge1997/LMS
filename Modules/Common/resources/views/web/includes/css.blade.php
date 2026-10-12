@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap 5 -->
@if($isRtl)
    <link href="{{ web_asset('css/bootstrap.rtl.min.css') }}" rel="stylesheet" type="text/css" />
@else
    <link href="{{ web_asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endif

<!-- General Custom Styles -->
<link href="{{ web_asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

<!-- Dynamic Tenant Theme -->
@if($theme = web_asset('css/theme.css', false))
    <link href="{{ $theme }}" rel="stylesheet" type="text/css" />
@endif

@yield('css')

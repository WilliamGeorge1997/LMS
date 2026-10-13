<!DOCTYPE html>
@php
    $isRtl = app()->getLocale() === 'ar';
    $siteTitle = tenant()?->name ?? config('app.name');
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteTitle }}@hasSection('title') - @yield('title')@endif</title>

    <!-- Favicon -->
    @if($favicon = web_asset('images/favicon.ico'))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif

    @include('common::web.includes.css')
</head>
<body>
    <main class="@yield('main_class', 'min-vh-100 d-flex flex-column justify-content-center align-items-center py-4 px-3')">
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    @include('common::web.includes.js')
</body>
</html>

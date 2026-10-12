<!DOCTYPE html>
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @include('common::web.includes.css')
</head>
<body>
    <main class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-4 px-3">
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    @include('common::web.includes.js')
</body>
</html>

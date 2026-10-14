@extends('common::web.layouts.master')

@section('main_class', 'min-vh-100 d-flex flex-column flex-lg-row p-0 bg-light')

@section('content')
    @include('common::web.includes.sidebar')

    <div class="flex-grow-1 d-flex flex-column min-vh-100" id="app-wrapper">
        <main id="app-content" class="flex-grow-1 p-3 p-sm-4 p-md-5">
            @yield('app_content')
        </main>
    </div>
@endsection

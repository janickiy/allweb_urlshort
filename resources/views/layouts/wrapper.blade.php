<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100" dir="{{ (__('lang_dir') == 'rtl' ? 'rtl' : 'ltr') }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site_title')</title>

    <link href="{{ asset('images/favicon.png') }}" rel="icon">


    <!-- Styles -->

    @hasSection('base_css')
        @yield('base_css')
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" id="app-css">
    @endif

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    @yield('css')

    <link href="{{ asset('css/theme-blue.css') }}" rel="stylesheet" id="theme-accent-css">

</head>

@yield('body')


<!-- Scripts -->
@hasSection('base_js')
    @yield('base_js')
@else
    <script src="{{ asset('js/app.js') }}" defer></script>
@endif


@yield('js')

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100" dir="{{ (__('lang_dir') == 'rtl' ? 'rtl' : 'ltr') }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('site_title')</title>

    <link href="{{ url('/') }}/uploads/brand/{{ config('settings.favicon') ?? 'favicon.png' }}" rel="icon">


    <!-- Styles -->

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" id="app-css">

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    @yield('css')

</head>

@yield('body')


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>


@yield('js')

</html>

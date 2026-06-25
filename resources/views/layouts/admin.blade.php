@extends('layouts.wrapper')

@php
    $adminTitle = __(Illuminate\Support\Str::headline(request()->segment(3) ?: request()->segment(2) ?: 'Dashboard'));
@endphp

@section('base_css')
    <!-- AdminLTE layout loads its own Vite CSS bundle. -->
@endsection

@section('css')
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
@endsection

@section('base_js')
    <!-- AdminLTE layout loads its own Vite JavaScript bundle. -->
@endsection

@section('site_title', formatTitle([$adminTitle, config('settings.title')]))

@section('body')
    <body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary adminlte-page">
        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body border-bottom" aria-label="{{ __('Admin navigation') }}">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link admin-sidebar-toggle" data-lte-toggle="sidebar" href="#" role="button" aria-label="{{ __('Menu') }}">
                                <span></span>
                                <span></span>
                                <span></span>
                            </a>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <a href="{{ route('home') }}" class="nav-link">{{ __('Website') }}</a>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <a href="{{ route('dashboard') }}" class="nav-link">{{ __('User panel') }}</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ gravatar(Auth::user()->email, 40) }}" class="user-image rounded-circle shadow-sm" alt="{{ Auth::user()->name }}">
                                <span class="d-none d-md-inline ms-2">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <li class="user-header text-bg-primary">
                                    <img src="{{ gravatar(Auth::user()->email, 96) }}" class="rounded-circle shadow" alt="{{ Auth::user()->name }}">
                                    <p class="mb-0">
                                        {{ Auth::user()->name }}
                                        <small>{{ Auth::user()->email }}</small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <a href="{{ route('settings') }}" class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                                    <a class="btn btn-default btn-flat float-end" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="brand-link">
                        <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}" class="brand-image opacity-75 shadow-sm" alt="{{ config('settings.title') }}">
                        <span class="brand-text fw-light">{{ config('settings.title') ?: config('info.software.name') }}</span>
                    </a>
                </div>

                <div class="sidebar-wrapper">
                    <nav class="mt-2" aria-label="{{ __('Admin menu') }}">
                        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                            @include('admin.sidebar')
                        </ul>
                    </nav>
                </div>
            </aside>

            <main class="app-main">
                <div class="app-content-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <h3 class="mb-0">{{ $adminTitle }}</h3>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Admin') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $adminTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="app-content">
                    <div class="container-fluid pb-4">
                        @yield('admin_content')
                    </div>
                </div>
            </main>

            <footer class="app-footer">
                <div class="float-end d-none d-sm-inline">
                    {{ __('Version') }} {{ config('info.software.version') }}
                </div>
                <strong>{{ config('info.software.name') }}</strong>
            </footer>
        </div>

        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        {!! config('settings.tracking_code') !!}
    </body>
@endsection

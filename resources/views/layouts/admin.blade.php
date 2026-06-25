@extends('layouts.wrapper')

@section('css')
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
@endsection

@section('body')
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body border-bottom">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <button class="nav-link btn btn-link" type="button" data-lte-toggle="sidebar" aria-label="{{ __('Menu') }}">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <a href="{{ route('dashboard') }}" class="nav-link">{{ __('User') }}</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="{{ route('settings') }}" class="nav-link d-flex align-items-center">
                                <img src="{{ gravatar(Auth::user()->email, 40) }}" class="rounded-circle admin-user-image" alt="{{ Auth::user()->name }}">
                                <span class="d-none d-sm-inline ms-2">{{ Auth::user()->name }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" title="{{ __('Logout') }}">
                                @include('icons.logout', ['class' => 'fill-current icon-text'])
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="brand-link">
                        <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}" class="brand-image opacity-75" alt="{{ config('settings.title') }}">
                        <span class="brand-text fw-light">{{ config('settings.title') }}</span>
                    </a>
                </div>

                <div class="sidebar-wrapper">
                    <nav class="mt-2">
                        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="{{ __('Menu') }}" data-accordion="false">
                            @include('admin.sidebar')
                        </ul>
                    </nav>
                </div>
            </aside>

            <main class="app-main">
                <div class="app-content">
                    <div class="container-fluid py-3">
                        @yield('admin_content')
                    </div>
                </div>
            </main>
        </div>

        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        {!! config('settings.tracking_code') !!}
    </body>
@endsection

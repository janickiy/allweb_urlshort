@extends('layouts.wrapper')

@php
    $adminTitleSegment = request()->segment(3) ?: request()->segment(2) ?: 'dashboard';
    $adminTitleKeys = [
        'captcha' => 'ui.nav.captcha',
        'contact' => 'ui.nav.contact',
        'dashboard' => 'ui.nav.dashboard',
        'domains' => 'ui.nav.domains',
        'edit' => 'ui.actions.edit',
        'email' => 'ui.nav.email',
        'general' => 'ui.nav.general',
        'invoice' => 'ui.nav.invoice',
        'legal' => 'ui.nav.legal',
        'links' => 'ui.nav.links',
        'new' => 'ui.actions.new',
        'pages' => 'ui.nav.pages',
        'payment' => 'ui.nav.payment',
        'plans' => 'ui.nav.plans',
        'registration' => 'ui.nav.registration',
        'settings' => 'ui.nav.settings',
        'shortener' => 'ui.nav.shortener',
        'social' => 'ui.nav.social',
        'subscriptions' => 'ui.nav.subscriptions',
        'users' => 'ui.nav.users',
        'workspaces' => 'ui.nav.workspaces',
    ];
    $adminTitle = isset($adminTitleKeys[$adminTitleSegment])
        ? __($adminTitleKeys[$adminTitleSegment])
        : __(Illuminate\Support\Str::headline($adminTitleSegment));
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
            <nav class="app-header navbar navbar-expand bg-body border-bottom" aria-label="{{ __('ui.admin.navigation') }}">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link admin-sidebar-toggle" data-lte-toggle="sidebar" href="#" role="button" aria-label="{{ __('ui.admin.menu') }}">
                                <span></span>
                                <span></span>
                                <span></span>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-center admin-top-menu">
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link d-inline-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('ui.actions.change_language') }}">
                                @include('icons.language', ['class' => 'fill-current icon-text'])
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach(config('app.locales', []) as $code => $name)
                                    <li>
                                        <form action="{{ route('locale') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="locale" value="{{ $code }}">
                                            <button type="submit" class="dropdown-item @if(app()->getLocale() === $code) active @endif">{{ $name }}</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                                @include('icons.user', ['class' => 'fill-current icon-text'])
                                <span>{{ Auth::user()->name }}</span>
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
                                    <a href="{{ route('settings') }}" class="btn btn-default btn-flat">{{ __('ui.admin.profile') }}</a>
                                    <a class="btn btn-default btn-flat float-end" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                        {{ __('ui.admin.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" aria-label="{{ __('ui.admin.logout') }}">
                                @include('icons.logout', ['class' => 'fill-current icon-text'])
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
                <div class="sidebar-brand admin-sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="brand-link">
                        <img src="{{ asset('uploads/brand/shortlink-pro-logo.svg') }}" class="brand-image admin-brand-image shadow-sm" alt="{{ config('settings.title') }}">
                        <span class="brand-text admin-brand-text">
                            <span class="admin-brand-title">{{ config('settings.title') ?: config('info.software.name') }}</span>
                            <span class="admin-brand-subtitle">{{ __('ui.admin.panel') }}</span>
                            <span class="admin-brand-rule">
                                <span></span><span></span><span></span><span></span>
                            </span>
                        </span>
                    </a>
                </div>

                <div class="sidebar-wrapper">
                    <nav class="mt-2" aria-label="{{ __('ui.admin.navigation_menu') }}">
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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('ui.admin.control_panel') }}</a></li>
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
                <strong>&copy; {{ now()->year }} {{ config('settings.title') ?: config('info.software.name') }}.</strong>
                <a href="{{ config('info.software.url') }}" target="_blank" rel="noopener">{{ config('info.software.author') }}</a>
            </footer>
        </div>

        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        {!! config('settings.tracking_code') !!}
    </body>
@endsection

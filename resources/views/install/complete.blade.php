@extends('layouts.install')

@section('content')
    <div class="install-result install-result--success">
        <span class="install-result__icon">&check;</span>
        <p class="install-eyebrow">{{ __('install.str.complete') }}</p>
        <h2>{{ __('install.str.well_done') }}</h2>
        <p>{{ __('install.str.app_is_successfully_installed') }}</p>
    </div>

    <div class="install-actions">
        <a href="{{ route('login') }}" class="install-button">
            {{ __('install.str.log_in') }}
        </a>
    </div>
@endsection

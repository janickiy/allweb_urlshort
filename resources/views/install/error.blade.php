@extends('layouts.install')

@section('content')
    <div class="install-result install-result--danger">
        <span class="install-result__icon">!</span>
        <p class="install-eyebrow">{{ __('install.str.install') }}</p>
        <h2>{{ __('install.str.error_title') }}</h2>
        <p>{{ __('install.str.error_text') }}</p>
    </div>

    <div class="install-actions">
        <a href="{{ route('install.database') }}" class="install-button">
            {{ __('install.str.try_again') }}
        </a>
    </div>
@endsection

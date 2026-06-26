@extends('layouts.install')

@section('content')
    <div class="install-heading">
        <p class="install-eyebrow">{{ __('install.str.system_requirements') }}</p>
        <h2>{{ __('install.str.system_requirements') }}</h2>
        <p>{{ __('install.str.requirements_text') }}</p>
    </div>

    <div class="install-check-list">
        @foreach($requirements as $label => $loaded)
            <div class="install-check-list__item">
                <span>{{ $label }}</span>
                <span class="install-badge {{ $loaded ? 'install-badge--success' : 'install-badge--danger' }}">
                    {{ $loaded ? 'OK' : 'FAIL' }}
                </span>
            </div>
        @endforeach
    </div>

    @unless($allLoaded)
        <div class="install-alert install-alert--danger">
            <strong>{{ __('install.str.requirements_failed_title') }}</strong>
            <span>{{ __('install.str.requirements_failed_text') }}</span>
        </div>
    @endunless

    <div class="install-actions">
        <a href="{{ route('install.start') }}" class="install-button install-button--secondary">
            {{ __('install.button.back') }}
        </a>
        @if($allLoaded)
            <a href="{{ route('install.permissions') }}" class="install-button">
                {{ __('install.button.next') }}
            </a>
        @endif
    </div>
@endsection

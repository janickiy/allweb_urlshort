@extends('layouts.install')

@section('content')
    <div class="install-heading">
        <p class="install-eyebrow">{{ __('install.str.permissions') }}</p>
        <h2>{{ __('install.str.access_rights') }}</h2>
        <p>{{ __('install.str.permissions_text') }}</p>
    </div>

    <div class="install-check-list">
        @foreach($permissions as $folder => $granted)
            <div class="install-check-list__item">
                <span>{{ $folder }}</span>
                <span class="install-badge {{ $granted ? 'install-badge--success' : 'install-badge--danger' }}">
                    {{ $granted ? 'OK' : 'FAIL' }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="install-actions">
        <a href="{{ route('install.requirements') }}" class="install-button install-button--secondary">
            {{ __('install.button.back') }}
        </a>
        @if($allGranted)
            <a href="{{ route('install.database') }}" class="install-button">
                {{ __('install.button.next') }}
            </a>
        @endif
    </div>
@endsection

@extends('layouts.install')

@section('content')
    <div class="install-heading">
        <p class="install-eyebrow">{{ __('install.str.administration') }}</p>
        <h2>{{ __('install.str.ready_to_install') }}</h2>
        <p>{{ __('install.str.admin_text') }}</p>
    </div>

    <form method="post" action="{{ route('install.install') }}" class="install-form install-form--compact" autocomplete="off">
        @csrf

        <label class="install-field">
            <span>{{ __('install.str.name') }}</span>
            <input type="text" name="name" value="{{ old('name', 'Admin') }}" autocomplete="name" required>
        </label>

        <label class="install-field">
            <span>{{ __('install.str.email') }}</span>
            <input type="email" name="email" value="{{ old('email', 'admin@example.test') }}" autocomplete="username" required>
        </label>

        <div class="install-grid install-grid--two">
            <label class="install-field">
                <span>{{ __('install.str.password') }}</span>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <label class="install-field">
                <span>{{ __('install.str.confirm_password') }}</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>
        </div>

        <div class="install-alert install-alert--info">
            <strong>{{ __('install.str.important') }}</strong>
            <span>{{ __('install.str.install_notice') }}</span>
        </div>

        <div class="install-actions">
            <a href="{{ route('install.database') }}" class="install-button install-button--secondary">
                {{ __('install.button.back') }}
            </a>
            <button type="submit" class="install-button">{{ __('install.button.install') }}</button>
        </div>
    </form>
@endsection

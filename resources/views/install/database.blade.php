@extends('layouts.install')

@section('content')
    <div class="install-heading">
        <p class="install-eyebrow">{{ __('install.str.database_info') }}</p>
        <h2>{{ __('install.str.database_information') }}</h2>
        <p>{{ __('install.str.database_text') }}</p>
    </div>

    <form method="post" action="{{ route('install.installation') }}" class="install-form" autocomplete="off">
        @csrf

        <div class="install-grid install-grid--two install-database-grid">
            <label class="install-field">
                <span>{{ __('install.str.database_host') }}</span>
                <input type="text" name="db_host" value="{{ old('db_host', config('database.connections.mysql.host', 'mysql')) }}" autocomplete="off" required>
                <small>{{ __('install.hint.database_host') }}</small>
            </label>

            <label class="install-field">
                <span>{{ __('install.str.database_port') }}</span>
                <input type="number" name="db_port" value="{{ old('db_port', config('database.connections.mysql.port', 3306)) }}" min="1" max="65535" autocomplete="off" required>
                <small>{{ __('install.hint.database_port') }}</small>
            </label>

            <label class="install-field">
                <span>{{ __('install.str.database_name') }}</span>
                <input type="text" name="db_database" value="{{ old('db_database', config('database.connections.mysql.database', 'allweb_urlshort')) }}" autocomplete="off" required>
                <small>{{ __('install.hint.database_name') }}</small>
            </label>

            <label class="install-field">
                <span>{{ __('install.str.database_username') }}</span>
                <input type="text" name="db_username" value="{{ old('db_username', config('database.connections.mysql.username', 'allweb')) }}" autocomplete="off" required>
                <small>{{ __('install.hint.database_username') }}</small>
            </label>

            <label class="install-field">
                <span>{{ __('install.str.password') }}</span>
                <input type="password" name="db_password" value="{{ old('db_password', config('database.connections.mysql.password', '')) }}" autocomplete="new-password" data-lpignore="true" data-1p-ignore>
                <small>{{ __('install.hint.database_password') }}</small>
            </label>
        </div>

        <div class="install-actions">
            <a href="{{ route('install.permissions') }}" class="install-button install-button--secondary">
                {{ __('install.button.back') }}
            </a>
            <button type="submit" class="install-button">{{ __('install.button.next') }}</button>
        </div>
    </form>
@endsection

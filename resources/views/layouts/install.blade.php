<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('install.str.installation') }} - {{ config('info.software.name') }}</title>

    <link href="{{ asset('uploads/brand/favicon.png') }}" rel="icon">
    <link rel="stylesheet" href="{{ asset('css/install.css') }}">
</head>
<body>
    <div class="install-shell">
        <aside class="install-sidebar">
            <a class="install-logo" href="{{ route('install.start') }}" aria-label="{{ config('info.software.name') }}">
                <img src="{{ asset('uploads/brand/shortlink-pro-logo.svg') }}" alt="{{ config('info.software.name') }}">
            </a>

            <div class="install-sidebar__intro">
                <p class="install-eyebrow">{{ __('install.str.installation') }}</p>
                <h1>{{ config('info.software.name') }}</h1>
                <p>{{ __('install.str.welcome') }}</p>
            </div>

            @include('install.steps')
        </aside>

        <main class="install-content">
            <div class="install-card">
                @if(count(config('app.locales', [])) > 1)
                    <div class="install-toolbar">
                        <label for="install_locale">{{ __('install.str.select_language') }}</label>
                        <select id="install_locale" class="install-language">
                            @foreach(config('app.locales', []) as $code => $languageName)
                                <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $languageName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($errors->any())
                    <div class="install-alert install-alert--danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.getElementById('install_locale')?.addEventListener('change', function () {
            fetch('{{ route('install.ajax') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    action: 'change_locale',
                    locale: this.value,
                }),
            }).then(function () {
                window.location.reload();
            });
        });
    </script>
</body>
</html>

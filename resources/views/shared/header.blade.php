@guest
<div id="header" class="header sticky-top shadow bg-base-0 z-1025">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light px-0 py-3">
            <a href="{{ route('home') }}" aria-label="{{ config('settings.title') }}" class="navbar-brand p-0 d-flex align-items-center text-decoration-none text-dark overflow-hidden">
                <div class="logo">
                    <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}">
                </div>
                <span class="font-weight-bold text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}">{{ config('settings.title') ?: config('info.software.name') }}</span>
            </a>
            <button class="navbar-toggler border-0 p-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav pt-2 p-lg-0 {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">
                    @if(config('settings.stripe'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pricing') }}" role="button">{{ __('Pricing') }}</a>
                        </li>
                    @endif

                    <li class="nav-item d-flex align-items-center" style="{{ __('lang_dir') == 'rtl' ? 'margin-left: 20px;' : 'margin-right: 20px;' }}">
                        @include('shared.language', [
                            'languageWrapperClass' => 'd-inline-flex',
                            'languageLinkClass' => 'text-secondary text-decoration-none d-flex align-items-center py-2'
                        ])
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}" role="button">{{ __('Login') }}</a>
                    </li>

                    @if(config('settings.registration_registration'))
                        <li class="nav-item d-flex align-items-center">
                            <a class="btn btn-outline-primary" href="{{ route('register') }}" role="button">{{ __('Register') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
</div>
@else
<div id="header" class="header sticky-top shadow bg-base-0 z-1025 d-lg-none">
    <div class="container-fluid">
        <nav class="navbar navbar-light px-0 py-3">
            <a href="{{ route('dashboard') }}" aria-label="{{ config('settings.title') }}" class="navbar-brand p-0 d-flex align-items-center text-decoration-none text-dark overflow-hidden">
                <div class="logo">
                    <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}">
                </div>
                <span class="font-weight-bold text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}">{{ config('settings.title') ?: config('info.software.name') }}</span>
            </a>
            <button class="slide-menu-toggle navbar-toggler border-0 p-0" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
    </div>
</div>

<div class="auth-top-actions d-none d-lg-flex align-items-center">
    @if(count(config('app.locales', [])) > 1)
        <div class="dropdown">
            <a href="#" class="auth-language-toggle d-flex align-items-center justify-content-center text-decoration-none bg-base-0 shadow-sm" id="auth-language-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('ui.actions.change_language') }}">
                @include('icons.language', ['class' => 'icon-text fill-current'])
                <span class="auth-language-code">{{ strtoupper(app()->getLocale()) }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="auth-language-menu">
                @foreach(config('app.locales', []) as $code => $name)
                    <form action="{{ route('locale') }}" method="post">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $code }}">
                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 @if(app()->getLocale() === $code) active @endif">
                            <span>{{ $name }}</span>
                            <span class="small text-uppercase">{{ $code }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <div class="dropdown auth-user-menu">
        <a href="#" class="auth-user-toggle d-flex align-items-center justify-content-center text-decoration-none bg-base-0 shadow-sm" id="auth-user-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('ui.dashboard.account') }}">
            <img src="{{ gravatar(Auth::user()->email, 72) }}" class="auth-user-avatar rounded-circle" alt="{{ Auth::user()->name }}">
        </a>

        <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="auth-user-menu">
            <div class="px-4 py-3">
                <div class="font-weight-medium text-dark text-truncate">{{ Auth::user()->name }}</div>
                <div class="small text-muted text-truncate">{{ Auth::user()->email }}</div>
            </div>
            <div class="dropdown-divider my-0"></div>

            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('settings') }}">
                @include('icons.settings', ['class' => 'icon-text fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                {{ __('ui.nav.settings') }}
            </a>

            @if(Auth::user()->role == 1)
                @if (request()->segment(1) == 'admin')
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('dashboard') }}">
                        @include('icons.user', ['class' => 'icon-text fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                        {{ __('ui.dashboard.user') }}
                    </a>
                @else
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.dashboard') }}">
                        @include('icons.admin', ['class' => 'icon-text fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                        {{ __('ui.admin.admin') }}
                    </a>
                @endif
            @endif

            <div class="dropdown-divider my-0"></div>
            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                @include('icons.logout', ['class' => 'icon-text fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                {{ __('ui.admin.logout') }}
            </a>
        </div>
    </div>
</div>

<nav class="slide-menu shadow bg-base-0 ct navbar navbar-light p-0 d-flex flex-column z-1025" id="slide-menu">
    <div class="sidebar-section flex-grow-1 d-flex flex-column w-100">
        <div>
            <div class="{{ (__('lang_dir') == 'rtl' ? 'pr-4' : 'pl-4') }} py-3 d-flex align-items-center">
                <a href="{{ route('dashboard') }}" aria-label="{{ config('settings.title') }}" class="navbar-brand p-0 d-flex align-items-center text-decoration-none text-dark overflow-hidden">
                    <div class="logo">
                        <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}">
                    </div>
                    <span class="font-weight-bold text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}">{{ config('settings.title') ?: config('info.software.name') }}</span>
                </a>
                <div class="close slide-menu-toggle cursor-pointer d-lg-none d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }} px-4 py-2">
                    @include('icons.close', ['class' => 'fill-current icon-close'])
                </div>
            </div>
        </div>

        <div class="sidebar-section flex-grow-1 overflow-auto sidebar">
            <div class="d-flex align-items-center">
                <div class="py-3 {{ (__('lang_dir') == 'rtl' ? 'pr-4 pl-0' : 'pl-4 pr-0') }} font-weight-medium text-muted text-uppercase flex-grow-1">{{ __('ui.admin.menu') }}</div>

                @if(Auth::user()->role == 1)
                    @if (request()->segment(1) == 'admin')
                        <a class="px-4 py-2 text-decoration-none text-secondary" href="{{ route('dashboard') }}" data-toggle="tooltip" title="{{ __('ui.dashboard.user') }}" role="button"><span class="d-flex align-items-center">@include('icons.user', ['class' => 'icon-text fill-current'])</span></a>
                    @else
                        <a class="px-4 py-2 text-decoration-none text-secondary" href="{{ route('admin.dashboard') }}" data-toggle="tooltip" title="{{ __('ui.admin.admin') }}" role="button"><span class="d-flex align-items-center">@include('icons.admin', ['class' => 'icon-text fill-current'])</span></a>
                    @endif
                @endif
            </div>

            <div class="nav flex-column">
                @yield('menu')
            </div>
        </div>

        <div class="py-3 px-4">
            <div class="progress w-100 my-2 sidebar-progress">
                <div class="progress-bar" role="progressbar" style="width: {{ ($userFeatures['option_links'] == 0 ? 100 : (($stats['links'] / $userFeatures['option_links']) * 100)) }}%"></div>
            </div>

            <div class="row no-gutters">
                <div class="col d-flex align-items-center">
                    <div class="small text-muted">
                         {{ __('ui.dashboard.links_created_progress', ['number' => $stats['links'], 'total' => ($userFeatures['option_links'] < 0 ? '∞' : $userFeatures['option_links'])]) }}
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'pr-2' : 'pl-2') }}">
                    <a href="{{ route('pricing') }}" class="text-secondary" data-toggle="tooltip" data-html="true" title="<div class='mx-2 font-size-base {{ (__('lang_dir') == 'rtl' ? 'text-right' : 'text-left') }}'><div class='row my-2'><div class='col'>{{ __('ui.nav.links') }}</div><div class='col-auto'>{{ __('ui.dashboard.quota_value', ['number' => $stats['links'], 'total' => ($userFeatures['option_links'] < 0 ? '∞' : $userFeatures['option_links'])]) }}</div></div><div class='row my-2'><div class='col'>{{ __('ui.nav.workspaces') }}</div><div class='col-auto'>{{ __('ui.dashboard.quota_value', ['number' => $stats['workspaces'], 'total' => ($userFeatures['option_workspaces'] < 0 ? '∞' : $userFeatures['option_workspaces'])]) }}</div></div><div class='row my-2'><div class='col'>{{ __('ui.nav.domains') }}</div><div class='col-auto'>{{ __('ui.dashboard.quota_value', ['number' => $stats['domains'], 'total' => ($userFeatures['option_domains'] < 0 ? '∞' : $userFeatures['option_domains'])]) }}</div></div></div>">@include('icons.info', ['class' => 'icon-text fill-current'])</a>
                </div>
            </div>

        </div>
        <div class="sidebar sidebar-footer d-lg-none">
            <div class="py-3 {{ (__('lang_dir') == 'rtl' ? 'pr-4 pl-0' : 'pl-4 pr-0') }} d-flex align-items-center" aria-expanded="true">
                <a href="{{ route('settings') }}" class="d-flex align-items-center overflow-hidden text-secondary text-decoration-none flex-grow-1">
                    <img src="{{ gravatar(Auth::user()->email, 72) }}" class="flex-shrink-0 rounded-circle {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                    <div class="d-flex flex-column text-truncate">
                        <div class="font-weight-medium text-dark text-truncate">
                            {{ Auth::user()->name }}
                        </div>

                        <div class="small font-weight-medium">
                            {{ __('ui.nav.settings') }}
                        </div>
                    </div>
                </a>

                <a class="py-2 px-4 d-flex flex-shrink-0 align-items-center text-secondary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-toggle="tooltip" title="{{ __('ui.admin.logout') }}">@include('icons.logout', ['class' => 'fill-current'])</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</nav>
@endguest

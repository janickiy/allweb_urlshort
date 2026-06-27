@php
    /**
     * key => [icon, title, route, [
     *  subKey => [title, route]
     * ]]
     */
    $menu = [
        'dashboard' => ['dashboard', 'ui.nav.dashboard', 'admin.dashboard'],
        'plans' => ['package', 'ui.nav.plans', 'admin.plans'],
        'subscriptions' => ['subscription', 'ui.nav.subscriptions', 'admin.subscriptions'],
        'links' => ['link', 'ui.nav.links', 'admin.links'],
        'workspaces' => ['workspace', 'ui.nav.workspaces', 'admin.workspaces'],
        'domains' => ['domain', 'ui.nav.domains', 'admin.domains'],
        'pages' => ['page', 'ui.nav.pages', 'admin.pages'],
        'settings' => ['settings', 'ui.nav.settings', null, [
            'general' => ['ui.nav.general', 'admin.settings.general'],
            'email' => ['ui.nav.email', 'admin.settings.email'],
            'social' => ['ui.nav.social', 'admin.settings.social'],
            'payment' => ['ui.nav.payment', 'admin.settings.payment'],
            'registration' => ['ui.nav.registration', 'admin.settings.registration'],
            'legal' => ['ui.nav.legal', 'admin.settings.legal'],
            'invoice' => ['ui.nav.invoice', 'admin.settings.invoice'],
            'contact' => ['ui.nav.contact', 'admin.settings.contact'],
            'captcha' => ['ui.nav.captcha', 'admin.settings.captcha'],
            'shortener' => ['ui.nav.shortener', 'admin.settings.shortener']
        ]],
        'users' => ['users', 'ui.nav.users', 'admin.users'],
    ];
@endphp

@foreach ($menu as $key => $value)
    @php
        $hasChildren = isset($value[3]);
        $isActive = request()->segment(2) == $key;
        $href = $hasChildren ? '#' : (Route::has($value[2]) ? route($value[2]) : $value[2]);
    @endphp

    <li class="nav-item @if($hasChildren && $isActive) menu-open @endif">
        <a
            class="nav-link @if($isActive && !$hasChildren) active @elseif($isActive && $hasChildren) active @endif"
            href="{{ $href }}"
            @if($hasChildren) role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" @endif
        >
            <span class="nav-icon sidebar-icon">@include('icons.' . $value[0], ['class' => 'fill-current'])</span>
            <p>
                {{ __($value[1]) }}
                @if ($hasChildren)
                    <span class="nav-arrow">@include('icons.expand', ['class' => 'fill-current'])</span>
                @endif
            </p>
        </a>

        @if ($hasChildren)
            <ul class="nav nav-treeview">
                @foreach ($value[3] as $subKey => $subValue)
                    <li class="nav-item">
                        <a href="{{ (Route::has($subValue[1]) ? route($subValue[1]) : $subValue[1]) }}" class="nav-link @if (request()->segment(3) == $subKey) active @endif">
                            <span class="nav-icon sidebar-sub-icon"></span>
                            <p>{{ __($subValue[0]) }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach

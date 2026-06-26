@php
    /**
     * key => [icon, title, route, [
     *  subKey => [title, route]
     * ]]
     */
    $menu = [
        'dashboard' => ['dashboard', 'Dashboard', 'admin.dashboard'],
        'plans' => ['package', 'Plans', 'admin.plans'],
        'subscriptions' => ['subscription', 'Subscriptions', 'admin.subscriptions'],
        'links' => ['link', 'Links', 'admin.links'],
        'workspaces' => ['workspace', 'Workspaces', 'admin.workspaces'],
        'domains' => ['domain', 'Domains', 'admin.domains'],
        'pages' => ['page', 'Pages', 'admin.pages'],
        'settings' => ['settings', 'Settings', null, [
            'general' => ['General', 'admin.settings.general'],
            'appearance' => ['Appearance', 'admin.settings.appearance'],
            'email' => ['Email', 'admin.settings.email'],
            'social' => ['Social', 'admin.settings.social'],
            'payment' => ['Payment', 'admin.settings.payment'],
            'registration' => ['Registration', 'admin.settings.registration'],
            'legal' => ['Legal', 'admin.settings.legal'],
            'invoice' => ['Invoice', 'admin.settings.invoice'],
            'contact' => ['Contact', 'admin.settings.contact'],
            'captcha' => ['Captcha', 'admin.settings.captcha'],
            'shortener' => ['Shortener', 'admin.settings.shortener']
        ]],
        'users' => ['users', 'Users', 'admin.users'],
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

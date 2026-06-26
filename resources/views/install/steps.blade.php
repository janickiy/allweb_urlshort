@php
    $steps = [
        ['route' => 'install.start', 'label' => __('install.str.license_agreement')],
        ['route' => 'install.requirements', 'label' => __('install.str.system_requirements')],
        ['route' => 'install.permissions', 'label' => __('install.str.access_rights')],
        ['route' => 'install.database', 'label' => __('install.str.database_info')],
        ['route' => 'install.admin', 'label' => __('install.str.administration')],
        ['route' => 'install.complete', 'label' => __('install.str.complete')],
    ];
    $routeNames = array_column($steps, 'route');
    $currentRoute = Route::currentRouteName();
    $currentIndex = array_search($currentRoute, $routeNames, true);
@endphp

<ol class="install-steps">
    @foreach($steps as $index => $step)
        @php
            $isCurrent = $step['route'] === $currentRoute;
            $isDone = $currentIndex !== false && $index < $currentIndex;
        @endphp
        <li class="{{ $isCurrent ? 'is-current' : '' }} {{ $isDone ? 'is-done' : '' }}">
            <span class="install-steps__number">{{ $index + 1 }}</span>
            <span class="install-steps__label">{{ $step['label'] }}</span>
        </li>
    @endforeach
</ol>

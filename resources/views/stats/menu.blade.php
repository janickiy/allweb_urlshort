@php
    /**
     * key => [icon, title, [route, parameter]]
     */
    $menu = [
        'general' => ['general', 'General', ['stats', $link->id]],
        'geographic' => ['geographic', 'Geographic', ['stats.geographic', $link->id]],
        'browsers' => ['browsers', 'Browsers', ['stats.browsers', $link->id]],
        'platforms' => ['platforms', 'Platforms', ['stats.platforms', $link->id]],
        'devices' => ['devices', 'Devices', ['stats.devices', $link->id]],
        'languages' => ['language', 'Languages', ['stats.languages', $link->id]],
        'sources' => ['link', 'Sources', ['stats.sources', $link->id]],
        'social' => ['social', 'Social', ['stats.social', $link->id]],
    ];
@endphp

<div class="d-flex">
    <div class="menu-underline flex-grow-1">
        <select class="custom-select my-4 d-block d-lg-none" name="stats-menu">
            @foreach ($menu as $key => $value)
                <option value="{{ route($value[2][0], $value[2][1]) }}" {{ request()->segment(3) == $key ? ' selected' : '' }}>{{ __($value[1]) }}</option>
            @endforeach
        </select>

        <ul class="nav mt-4 d-none d-lg-flex">
            @foreach ($menu as $key => $value)
                <li class="nav-item {{ (!$loop->last ? (__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4') : '') }}">
                    <a class="nav-link d-flex align-items-center font-weight-medium px-0 py-3 {{ (!$loop->last ? (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') : '') }} @if (Request::route()->getName() == $value[2][0]) active @endif" href="{{ route($value[2][0], $value[2][1]) }}">
                        <span class="d-flex align-items-center">@include('icons.' . $value[0], ['class' => 'icon-button '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])</span>
                        <span class="{{ (__('lang_dir') == 'rtl' ? 'mr-1' : 'ml-1') }}">{{ __($value[1]) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@if(! isset($admin))
    @section('site_title', formatTitle([__('Edit'), __('Link'), config('settings.title')]))

    @include('shared.breadcrumbs', ['breadcrumbs' => [
        ['url' => route('dashboard'), 'title' => __('Home')],
        ['url' => route('links'), 'title' => __('Links')],
        ['title' => __('Edit')],
    ]])

    <div class="d-flex">
        <h2 class="mb-0 flex-grow-1 text-break">{{ __('Edit') }}</h2>

        <div class="d-flex align-items-center flex-grow-0">
            @include('shared.buttons.copy_link')
            @include('shared.dropdowns.link', ['options' => ['dropdown' => ['button' => true, 'edit' => true, 'share' => true, 'stats' => true, 'open' => true, 'delete' => true]]])
        </div>
    </div>
@endif

<div class="{{ isset($admin) ? 'card card-primary card-outline shadow-sm mb-0 admin-form-card admin-link-edit-card' : 'card border-0 shadow-sm mt-3' }}">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                @if(isset($admin))
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.link', ['class' => 'fill-current icon-text'])
                        {{ __('Link') }}
                    </h3>
                @else
                    <div class="font-weight-medium py-1">{{ __('Link') }}</div>
                @endif
            </div>
            @if(isset($admin))
                <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
                    @include('shared.buttons.copy_link')
                    @include('shared.dropdowns.link', ['options' => ['dropdown' => ['button' => true, 'edit' => true, 'share' => true, 'stats' => true, 'open' => true, 'delete' => true]]])
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ isset($admin) ? route('admin.links.edit', $link->id) : route('links.edit', $link->id) }}" method="post" enctype="multipart/form-data" autocomplete="off" class="{{ isset($admin) ? 'admin-link-edit-form' : '' }}">
            @csrf

            @if(isset($admin))
                <input type="hidden" name="user_id" value="{{ $link->user?->id }}">
            @endif

            <div class="row g-3 align-items-start">
                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <label class="form-label" for="i_url">{{ __('Link') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <input type="text" name="url" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" autocapitalize="none" spellcheck="false" id="i_url" value="{{ old('url') ?? $link->url }}">
                            <div class="input-group-append" data-toggle="tooltip" title="{{ __('UTM Builder') }}">
                                <a class="btn input-group-text bg-transparent text-secondary text-hover d-flex align-items-center" href="#" data-toggle="modal" data-target="#utmModal" id="utm_builder">
                                    @include('icons.tag', ['class' => 'fill-current icon-button'])
                                </a>
                            </div>
                        </div>
                        @if ($errors->has('url'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('url') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <label class="form-label" for="i_alias">{{ __('Alias') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.alias', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <div class="input-group-prepend d-none d-md-block">
                                <span class="input-group-text">{{ str_replace(['http://', 'https://'], '', ($link->domain ? $link->domain->name : config('app.url'))) }}/</span>
                            </div>
                            <input type="text" name="alias" class="form-control{{ $errors->has('alias') ? ' is-invalid' : '' }}" autocapitalize="none" spellcheck="false" id="i_alias" value="{{ old('alias') ?? $link->alias }}">
                        </div>
                        @if ($errors->has('alias'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('alias') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_space_new">{{ __('Workspace') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('workspaces', ['App\Models\Link', $userFeatures['option_workspaces']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.workspace', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <select name="workspace" id="i_space" class="form-select{{ $errors->has('workspace') ? ' is-invalid' : '' }}" @cannot('workspaces', ['App\Models\Link', $userFeatures['option_workspaces']]) disabled @endcan>
                                <option value="">{{ __('None') }}</option>
                                @foreach($workspaces as $workspace)
                                    <option value="{{ $workspace->id }}" @if($link->workspace_id == $workspace->id || $workspace->id == old('workspace')) selected @endif>{{ $workspace->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('workspace'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('workspace') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_password">{{ __('Password') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('password', ['App\Models\Link', $userFeatures['option_password']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.security', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="i_password" value="{{ old('password') ?? $link->password }}" autocomplete="new-password" @cannot('password', ['App\Models\Link', $userFeatures['option_password']]) disabled @endcan>
                        </div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_expiration_date">{{ __('Expiration date') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('expiration', ['App\Models\Link', $userFeatures['option_expiration']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.calendar', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <input type="date" name="expiration_date" class="form-control{{ $errors->has('expiration_date') ? ' is-invalid' : '' }}" id="i_expiration_date" placeholder="YYYY-MM-DD" value="{{ old('expiration_date') ?? ($link->ends_at ? $link->ends_at->format('Y-m-d') : '') }}" @cannot('expiration', ['App\Models\Link', $userFeatures['option_expiration']]) disabled @endcan>
                            <input type="time" name="expiration_time" class="form-control{{ $errors->has('expiration_time') ? ' is-invalid' : '' }}" placeholder="HH:MM" value="{{ old('expiration_time') ?? ($link->ends_at ? $link->ends_at->format('H:i') : '') }}" @cannot('expiration', ['App\Models\Link', $userFeatures['option_expiration']]) disabled @endcan>
                            <div class="input-group-append">
                                <div class="input-group-text">@include('icons.expire', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                        </div>
                        <div class="row g-0">
                            <div class="col">
                                @if ($errors->has('expiration_date'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('expiration_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col">
                                @if ($errors->has('expiration_time'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('expiration_time') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_expiration_url">{{ __('Expiration link') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('expiration', ['App\Models\Link', $userFeatures['option_expiration']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <input type="text" name="expiration_url" id="i_expiration_url" class="form-control{{ $errors->has('expiration_url') ? ' is-invalid' : '' }}" autocapitalize="none" spellcheck="false" value="{{ old('expiration_url') ?? $link->expiration_url }}" @cannot('expiration', ['App\Models\Link', $userFeatures['option_expiration']]) disabled @endcan>
                        </div>
                        @if ($errors->has('expiration_url'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('expiration_url') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_public">{{ __('Stats') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('stats', ['App\Models\Link', $userFeatures['option_stats']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.stats', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <select name="public" id="i_public" class="form-select{{ $errors->has('public') ? ' is-invalid' : '' }}" @cannot('expiration', ['App\Models\Link', $userFeatures['option_stats']]) disabled @endcan>
                                @foreach([0 => __('Private'), 1 => __('Public')] as $key => $value)
                                    <option value="{{ $key }}" @if ((old('public') !== null && old('public') == $key) || $link->public == $key) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('public'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('public') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <label class="form-label" for="i_disabled">{{ __('Disabled') }}</label>
                            </div>
                            <div class="col-auto">
                                @cannot('disabled', ['App\Models\Link', $userFeatures['option_disabled']])
                                    @if(config('settings.stripe'))
                                        <a href="{{ route('pricing') }}" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'fill-current text-primary icon-text'])</a>
                                    @endif
                                @endcannot
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@include('icons.block', ['class' => 'icon-label fill-current text-muted'])</div>
                            </div>
                            <select name="disabled" id="i_disabled" class="form-select{{ $errors->has('disabled') ? ' is-invalid' : '' }}" @cannot('disabled', ['App\Models\Link', $userFeatures['option_disabled']]) disabled @endcan>
                                @foreach([0 => __('No'), 1 => __('Yes')] as $key => $value)
                                    <option value="{{ $key }}" @if ((old('disabled') !== null && old('disabled') == $key) || $link->disabled == $key) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('disabled'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('disabled') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="hr-text"><span class="font-weight-medium text-muted">{{ __('Geotargeting') }}</span></div>

            <div id="geo-container" class="{{ isset($admin) ? 'admin-link-target-section' : '' }}">
                <input name="geo[empty][key]" type="hidden" disabled>
                <input name="geo[empty][value]" type="hidden" disabled>

                <div class="input-content">
                    <div class="row mb-3 mb-md-0 d-none input-template">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">@include('icons.geographic', ['class' => 'icon-label fill-current text-muted'])</div>
                                    </div>
                                    <select name="geo_key[]" data-input="key" class="form-select" disabled>
                                        <option value="" selected>{{ __('Country') }}</option>
                                        @foreach(config('countries') as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row g-2">
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                                            </div>
                                            <input type="text" data-input="value" class="form-control" autocapitalize="none" spellcheck="false" value="" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-auto mb-3 d-flex align-items-start">
                                    <button type="button" class="btn btn-outline-danger d-flex align-items-center input-delete">@include('icons.delete', ['class' => 'icon-button fill-current'])&#8203;</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $normalizeTargetList = static function (mixed $target): array {
                            if (! $target) {
                                return [];
                            }

                            if (is_string($target)) {
                                $decoded = json_decode($target, true);
                                $target = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                            }

                            if (is_object($target)) {
                                $target = json_decode(json_encode($target), true);
                            }

                            if (! is_array($target)) {
                                return [];
                            }

                            return array_values(array_filter(array_map(static function (mixed $value, int|string $key): ?array {
                                if (is_object($value)) {
                                    $value = json_decode(json_encode($value), true);
                                }

                                if (is_array($value)) {
                                    $ruleKey = $value['key'] ?? null;
                                    $ruleValue = $value['value'] ?? null;
                                } else {
                                    $ruleKey = is_string($key) ? $key : null;
                                    $ruleValue = $value;
                                }

                                if ($ruleKey === null || $ruleKey === '' || $ruleValue === null || $ruleValue === '') {
                                    return null;
                                }

                                return [
                                    'key' => (string) $ruleKey,
                                    'value' => (string) $ruleValue,
                                ];
                            }, $target, array_keys($target))));
                        };

                        $geoList = old('geo') ? $normalizeTargetList(old('geo')) : $normalizeTargetList($link->geo_target);
                    @endphp

                    @foreach($geoList as $id => $geo)
                        <div class="row mb-3 mb-md-0">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">@include('icons.geographic', ['class' => 'icon-label fill-current text-muted'])</div>
                                        </div>
                                        <select name="geo[{{ $id }}][key]" data-input="key" class="form-select{{ $errors->has('geo.'.$id.'.key') ? ' is-invalid' : '' }}">
                                            <option value="">{{ __('Country') }}</option>
                                            @foreach(config('countries') as $key => $value)
                                                <option value="{{ $key }}" @if($geo['key'] == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('geo.'.$id.'.key'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $errors->first('geo.'.$id.'.key') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                                                </div>
                                                <input type="text" name="geo[{{ $id }}][value]" data-input="value" class="form-control{{ $errors->has('geo.'.$id.'.value') ? ' is-invalid' : '' }}" autocapitalize="none" spellcheck="false" value="{{ $geo['value'] }}">
                                            </div>
                                            @if ($errors->has('geo.'.$id.'.value'))
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $errors->first('geo.'.$id.'.value') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-auto mb-3 d-flex align-items-start">
                                        <button type="button" class="btn btn-outline-danger d-flex align-items-center input-delete">@include('icons.delete', ['class' => 'icon-button fill-current'])&#8203;</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @can('geo', ['App\Models\Link', $userFeatures['option_geo']])
                    <button type="button" class="btn btn-outline-primary input-add d-inline-flex align-items-center">@include('icons.add', ['class' => 'icon-button fill-current'])&#8203;</button>
                @else
                    @if(config('settings.stripe'))
                        <a href="{{ route('pricing') }}" class="btn btn-outline-primary d-inline-flex align-items-center" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'icon-button fill-current'])&#8203;</a>
                    @endif
                @endcan
            </div>

            <div class="hr-text"><span class="font-weight-medium text-muted">{{ __('Platform targeting') }}</span></div>

            <div id="platform-container" class="{{ isset($admin) ? 'admin-link-target-section' : '' }}">
                <input name="platform[empty][key]" type="hidden" disabled>
                <input name="platform[empty][value]" type="hidden" disabled>

                <div class="input-content">
                    <div class="row mb-3 mb-md-0 d-none input-template">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">@include('icons.platforms', ['class' => 'icon-label fill-current text-muted'])</div>
                                    </div>
                                    <select name="platform_key[]" data-input="key" class="form-select" disabled>
                                        <option value="" selected>{{ __('Platform') }}</option>
                                        @foreach(config('platforms') as $platform)
                                            <option value="{{ $platform }}">{{ $platform }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row g-2">
                                <div class="col">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                                            </div>
                                            <input type="text" data-input="value" class="form-control" autocapitalize="none" spellcheck="false" value="" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-auto mb-3 d-flex align-items-start">
                                    <button type="button" class="btn btn-outline-danger d-flex align-items-center input-delete">@include('icons.delete', ['class' => 'icon-button fill-current'])&#8203;</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $platformList = old('platform') ? $normalizeTargetList(old('platform')) : $normalizeTargetList($link->platform_target);
                    @endphp

                    @foreach($platformList as $id => $platform)
                        <div class="row mb-3 mb-md-0">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">@include('icons.platforms', ['class' => 'icon-label fill-current text-muted'])</div>
                                        </div>
                                        <select name="platform[{{ $id }}][key]" data-input="key" class="form-select{{ $errors->has('platform.'.$id.'.key') ? ' is-invalid' : '' }}">
                                            <option value="">{{ __('Platform') }}</option>
                                            @foreach(config('platforms') as $value)
                                                <option value="{{ $value }}" @if($platform['key'] == $value) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('platform.'.$id.'.key'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $errors->first('platform.'.$id.'.key') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">@include('icons.link', ['class' => 'icon-label fill-current text-muted'])</div>
                                                </div>
                                                <input type="text" name="platform[{{ $id }}][value]" data-input="value" class="form-control{{ $errors->has('platform.'.$id.'.value') ? ' is-invalid' : '' }}" autocapitalize="none" spellcheck="false" value="{{ $platform['value'] }}">
                                            </div>
                                            @if ($errors->has('platform.'.$id.'.value'))
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $errors->first('platform.'.$id.'.value') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-auto mb-3 d-flex align-items-start">
                                        <button type="button" class="btn btn-outline-danger d-flex align-items-center input-delete">@include('icons.delete', ['class' => 'icon-button fill-current'])&#8203;</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @can('platform', ['App\Models\Link', $userFeatures['option_platform']])
                    <button type="button" class="btn btn-outline-primary input-add d-inline-flex align-items-center">@include('icons.add', ['class' => 'icon-button fill-current'])&#8203;</button>
                @else
                    @if(config('settings.stripe'))
                        <a href="{{ route('pricing') }}" class="btn btn-outline-primary d-inline-flex align-items-center" data-toggle="tooltip" title="{{ __('Unlock feature') }}">@include('icons.unlock', ['class' => 'icon-button fill-current'])&#8203;</a>
                    @endif
                @endcan
            </div>

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-4 pt-3 border-top">
                <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                    {{ __('Save') }}
                </button>
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" data-toggle="modal" data-target="#deleteLinkModal" data-action="{{ isset($admin) ? route('admin.links.delete', $link->id) : route('links.delete', $link->id) }}" data-text="{{ __('Are you sure you want to delete :name?', ['name' => (str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))))]) }}">
                    @include('icons.delete', ['class' => 'fill-current icon-button-sm'])
                    {{ __('Delete') }}
                </button>
            </div>
        </form>
    </div>
</div>

@if(isset($admin))
    @if(isset($link->user))
        <div class="card card-secondary card-outline shadow-sm mt-3">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md">
                        <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                            @include('icons.user', ['class' => 'fill-current icon-text'])
                            {{ __('User') }}
                        </h3>
                    </div>
                    <div class="col-12 col-md-auto">
                        <a href="{{ route('admin.users.edit', $link->user->id) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                            @include('icons.edit', ['class' => 'fill-current icon-button-sm'])
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body mb-n3">
                <div class="row">
                    <div class="col-12 col-xl-6 mb-3">
                        <div class="text-muted">{{ __('Name') }}</div>
                        <div>{{ $link->user->name }}</div>
                    </div>

                    <div class="col-12 col-xl-6 mb-3">
                        <div class="text-muted">{{ __('Email') }}</div>
                        <div>{{ $link->user->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@include('shared.modals.utm')
@include('shared.modals.share_link', ['admin' => isset($admin) ? true : false])
@include('shared.modals.delete_link', ['admin' => isset($admin) ? true : false])

@if(! isset($admin))
    @section('site_title', formatTitle([__('Account'), config('settings.title')]))

    @include('shared.breadcrumbs', ['breadcrumbs' => [
        ['url' => route('dashboard'), 'title' => __('Home')],
        ['url' => route('settings'), 'title' => __('Settings')],
        ['title' => __('Account')]
    ]])

    <div class="d-flex">
        <h2 class="mb-3 text-break">{{ __('Account') }}</h2>
    </div>
@endif

<div class="{{ isset($admin) ? 'card card-primary card-outline shadow-sm mb-0 admin-form-card admin-user-edit-card' : 'card border-0 shadow-sm' }}">
    <div class="card-header">
        @if(isset($admin))
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.user', ['class' => 'fill-current icon-text'])
                        {{ __('User') }}
                    </h3>
                </div>
            </div>
        @else
            <div class="font-weight-medium py-1">
                {{ __('Account') }}
            </div>
        @endif
    </div>
    <div class="card-body">
        @include('shared.message')

        @if(isset($admin) && $user->trashed())
            <div class="alert alert-danger" role="alert">
                {{ __(':name is disabled.', ['name' => $user->name]) }}
            </div>
        @endif

        <form action="{{ isset($admin) ? route('admin.users.edit', $user->id) : route('settings.account.update') }}" method="post" enctype="multipart/form-data" class="{{ isset($admin) ? 'admin-user-edit-form' : '' }}">
            @csrf

            <div class="mb-3">
                <label for="i_name" class="form-label">{{ __('Name') }}</label>
                <input type="text" name="name" id="i_name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') ?? $user->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="i_email" class="form-label">{{ __('Email') }}</label>
                <input type="text" name="email" id="i_email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') ?? $user->email }}">
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="i_timezone" class="form-label">{{ __('Timezone') }}</label>
                <select name="timezone" id="i_timezone" class="form-select{{ $errors->has('timezone') ? ' is-invalid' : '' }}">
                    @foreach(timezone_identifiers_list() as $value)
                        <option value="{{ $value }}" @if ($value == $user->timezone) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('timezone'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('timezone') }}</strong>
                    </span>
                @endif
            </div>

            @if(isset($admin))
                <div class="mb-3">
                    <label for="i_role" class="form-label">{{ __('Role') }}</label>
                    <select name="role" id="i_role" class="form-select{{ $errors->has('role') ? ' is-invalid' : '' }}">
                        @foreach([0 => __('User'), 1 => __('Admin')] as $key => $value)
                            <option value="{{ $key }}" @if ($key == $user->role) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('role'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('role') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-4 pt-3 border-top">
                <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                    {{ __('Save') }}
                </button>
                @if(isset($admin))
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                            @include('icons.horizontal_menu', ['class' => 'fill-current icon-button-sm'])
                            {{ __('More') }}
                        </button>
                        <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-end') }} border-0 shadow">
                            @if($user->trashed())
                                <a class="dropdown-item text-success d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#restoreModal">@include('icons.restore', ['class' => 'fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Restore') }}</a>
                                <div class="dropdown-divider"></div>
                            @else
                                <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#disableModal">@include('icons.block', ['class' => 'fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Disable') }}</a>
                            @endif
                            <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">@include('icons.delete', ['class' => 'fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Delete') }}</a>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

@if(isset($admin))
    <div class="row g-3 mt-3">
        @php
            $menu = [
                ['icon' => 'icons.subscription', 'route' => 'admin.subscriptions', 'title' => __('Subscriptions'), 'stats' => 'subscriptions'],
                ['icon' => 'icons.link', 'route' => 'admin.links', 'title' => __('Links'), 'stats' => 'links'],
                ['icon' => 'icons.workspace', 'route' => 'admin.workspaces', 'title' => __('Workspaces'), 'stats' => 'workspaces'],
                ['icon' => 'icons.domain', 'route' => 'admin.domains', 'title' => __('Domains'), 'stats' => 'domains']
            ];
        @endphp

        @foreach($menu as $link)
            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ route($link['route'], ['user_id' => $user->id]) }}" class="text-decoration-none text-muted text-hover">
                    <div class="card card-secondary card-outline h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            @include($link['icon'], ['class' => 'fill-current icon-text mr-3'])
                            <div>{{ $link['title'] }}</div>
                            @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
                            <div class="ml-auto badge text-bg-primary">{{ number_format($stats[$link['stats']], 0, __('.'), __(',')) }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('Delete') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="post" enctype="multipart/form-data">

                        @csrf

                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="disableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('Disable') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(config('settings.stripe'))
                    <div class="mb-3">{{ __('Disabling this account will cancel any active subscription.') }}</div>
                    @endif
                    <div>{{ __('Are you sure you want to disable :name?', ['name' => $user->name]) }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('admin.users.disable', $user->id) }}" method="post" enctype="multipart/form-data">

                        @csrf

                        <button type="submit" class="btn btn-danger">{{ __('Disable') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('Restore') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(config('settings.stripe'))
                    <div class="mb-3">{{ __('Restoring this account will resume any previously active subscription.') }}</div>
                    @endif
                    <div>{{ __('Are you sure you want to restore :name?', ['name' => $user->name]) }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('admin.users.restore', $user->id) }}" method="post" enctype="multipart/form-data">

                        @csrf

                        <button type="submit" class="btn btn-success">{{ __('Restore') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@section('site_title', formatTitle([__('ui.nav.workspaces'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('ui.nav.home')],
    ['title' => __('ui.nav.workspaces')]
]])

<div class="d-flex">
    <div class="flex-grow-1">
        <h2 class="mb-3 d-inline-block">{{ __('ui.nav.workspaces') }}</h2>
    </div>
    <div>
        <a href="{{ route('workspaces.new') }}" class="btn btn-primary mb-3">{{ __('ui.actions.new') }}</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('ui.nav.workspaces') }}</div></div>
            <div class="col-auto">
                <form method="GET" action="{{ route('workspaces') }}">
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="{{ __('ui.actions.search') }}" value="{{ app('request')->input('search') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn {{ request()->input('sort') ? 'btn-primary' : 'btn-outline-primary' }} d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.filter', ['class' => 'fill-current icon-button-sm'])&#8203;</button>
                            <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow" id="search-filters">
                                <div class="dropdown-header py-1">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark">{{ __('ui.actions.filters') }}</div></div>
                                        <div class="col-auto">
                                            @if(request()->input('sort'))
                                                <a href="{{ route('workspaces') }}" class="text-secondary">{{ __('ui.actions.reset') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="form-group px-4">
                                    <label for="i_sort" class="small">{{ __('ui.workspaces.sort') }}</label>
                                    <select name="sort" id="i_sort" class="custom-select custom-select-sm">
                                        @foreach(['desc' => __('ui.workspaces.descending'), 'asc' => __('ui.workspaces.ascending')] as $key => $value)
                                            <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group px-4 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('ui.actions.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        @if(count($workspaces) == 0)
            {{ __('ui.workspaces.no_results_found') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-6 d-flex">
                                    {{ __('ui.workspaces.name') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2">
                                    {{ __('ui.workspaces.color') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2">
                                    {{ __('ui.workspaces.created_at') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2">
                                    {{ __('ui.workspaces.links') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-outline-primary btn-sm invisible">{{ __('ui.actions.edit') }}</a>
                        </div>
                    </div>
                </div>

                @foreach($workspaces as $workspace)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-6 d-flex">
                                        <div class="text-truncate">
                                            <div class="d-flex">
                                                <div class="text-truncate">
                                                    <a href="{{ route('workspaces.edit', ['id' => $workspace->id]) }}">{{ $workspace->name }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none d-lg-block col-lg-2">
                                        <div class="icon-label rounded-circle bg-{{ formatWorkspace()[$workspace->color] }}"></div>
                                    </div>

                                    <div class="d-none d-lg-block col-lg-2">
                                        {{ $workspace->created_at->diffForHumans() }}
                                    </div>

                                    <div class="d-none d-lg-block col-lg-2">
                                        <a href="{{ route('links', ['workspace' => $workspace->id]) }}" class="text-dark">{{ $workspace->totalLinks }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('workspaces.edit', $workspace->id) }}" class="btn btn-outline-primary btn-sm">{{ __('ui.actions.edit') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('ui.workspaces.showing', ['from' => $workspaces->firstItem(), 'to' => $workspaces->lastItem(), 'total' => $workspaces->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $workspaces->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

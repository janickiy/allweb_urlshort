<div class="card border-0 shadow-sm mb-3" style="{{ __('lang_dir') == 'rtl' ? 'border-right' : 'border-left' }}: .25rem solid #0ea5e9 !important;">
    <div class="card-body py-3 px-4">
        <p class="mb-0 text-dark">{{ __('This section is used to manage all branded domains connected by users across the application. Review domain ownership, linked records, assigned links, and user accounts, filter the list, open related links, and edit or moderate domains when administrative changes are required.') }}</p>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm mb-0 admin-list-card">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                    @include('icons.domain', ['class' => 'fill-current icon-text'])
                    {{ __('Domains') }}
                </h3>
            </div>
            <div class="col-12 col-md-auto">
                <form method="GET" action="{{ route('admin.domains') }}" class="d-md-flex admin-filter-form">
                    @include('shared.filter_tags')
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ app('request')->input('search') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn {{ request()->input('sort') ? 'btn-primary' : 'btn-outline-primary' }} d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.filter', ['class' => 'fill-current icon-button-sm'])&#8203;</button>
                            <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow" id="search-filters">
                                <div class="dropdown-header py-1">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark">{{ __('Filters') }}</div></div>
                                        <div class="col-auto">
                                            @if(request()->input('sort'))
                                                <a href="{{ route('admin.domains') }}" class="text-secondary">{{ __('Reset') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="mb-3 px-4">
                                    <label for="i_sort" class="form-label small">{{ __('Sort') }}</label>
                                    <select name="sort" id="i_sort" class="form-select form-select-sm">
                                        @foreach(['desc' => __('Descending'), 'asc' => __('Ascending')] as $key => $value)
                                            <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="px-4 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('Search') }}</button>
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

        @if(count($domains) == 0)
            {{ __('No results found.') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-5 d-flex">
                                    {{ __('Name') }}
                                </div>

                                <div class="col-12 col-lg-5 d-flex">
                                    {{ __('User') }}
                                </div>

                                <div class="col-12 col-lg-2 d-flex">
                                    {{ __('Links') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-outline-primary btn-sm invisible">{{ __('Edit') }}</a>
                        </div>
                    </div>
                </div>

                @foreach($domains as $domain)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-5 d-flex">
                                        <div class="text-truncate">
                                            <div class="d-flex">
                                                <div class="text-truncate">
                                                    <a href="{{ route('admin.domains.edit', $domain->id) }}">{{ str_replace(['http://', 'https://'], '', $domain->name) }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-lg-5 d-flex align-items-center">
                                        <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }} list-avatar">
                                            <img src="{{ gravatar($domain->user->email, 48) }}" class="rounded-circle">
                                        </div>
                                        <a href="{{ route('admin.users.edit', $domain->user->id) }}"@if($domain->user->trashed()) class="text-danger" @endif>{{ $domain->user->name }}</a>
                                    </div>

                                    <div class="col-12 col-lg-2 d-flex">
                                        <a href="{{ route('admin.links', ['domain_id' => $domain->id]) }}" class="text-dark">{{ $domain->totalLinks }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.domains.edit', $domain->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('Showing :from-:to of :total', ['from' => $domains->firstItem(), 'to' => $domains->lastItem(), 'total' => $domains->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $domains->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

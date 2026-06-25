<div class="card card-primary card-outline shadow-sm mb-0 admin-list-card">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                    @include('icons.language', ['class' => 'fill-current icon-text'])
                    {{ __('Languages') }}
                </h3>
            </div>
            <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
                <a href="{{ route('admin.languages.new') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2">
                    @include('icons.add', ['class' => 'fill-current icon-button-sm'])
                    {{ __('New') }}
                </a>
                <form method="GET" action="{{ route('admin.languages') }}" class="admin-filter-form">
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
                                                <a href="{{ route('admin.languages') }}" class="text-secondary">{{ __('Reset') }}</a>
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

        @if(count($languages) == 0)
            {{ __('No results found.') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">{{ __('Name') }}</div>
                        <div class="col-auto">
                            <div class="btn btn-outline-primary btn-sm invisible">{{ __('Edit') }}</div>
                        </div>
                    </div>
                </div>

                @foreach($languages as $language)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <div>
                                    <a href="{{ route('admin.languages.edit', $language['code']) }}">{{ $language['name'] }}</a> @if($language['default']) <span class="badge badge-secondary">{{ __('Default') }}</span>@endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.languages.edit', $language['code']) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('Showing :from-:to of :total', ['from' => $languages->firstItem(), 'to' => $languages->lastItem(), 'total' => $languages->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $languages->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card card-primary card-outline shadow-sm mb-0 admin-list-card">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                    @include('icons.users', ['class' => 'fill-current icon-text'])
                    {{ __('Users') }}
                </h3>
            </div>
            <div class="col-12 col-md-auto">
                <form method="GET" action="{{ route('admin.users') }}" class="admin-filter-form">
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
                                                <a href="{{ route('admin.users') }}" class="text-secondary">{{ __('Reset') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="mb-3 px-4">
                                    <label for="i_role" class="form-label small">{{ __('Role') }}</label>
                                    <select name="role" id="i_role" class="form-select form-select-sm">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach([0 => __('User'), 1 => __('Admin')] as $key => $value)
                                            <option value="{{ $key }}" @if(request()->input('role') == $key && request()->input('role') !== null) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 px-4">
                                    <label for="i_by" class="form-label small">{{ __('Search by') }}</label>
                                    <select name="by" id="i_by" class="form-select form-select-sm">
                                        @foreach(['name' => __('Name'), 'email' => __('Email')] as $key => $value)
                                            <option value="{{ $key }}" @if(request()->input('by') == $key || !request()->input('by') && $key == 'name') selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

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

        @if(count($users) == 0)
            {{ __('No results found.') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row">
                                <div class="col-12 col-lg-6">{{ __('Name') }}</div>
                                <div class="col-12 col-lg-6">{{ __('Email') }}</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn btn-outline-primary btn-sm invisible">{{ __('Edit') }}</div>
                        </div>
                    </div>
                </div>
                @foreach($users as $user)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="row">
                                    <div class="col-12 col-lg-6 d-flex align-items-center">
                                        <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }} list-avatar">
                                            <img src="{{ gravatar($user->email, 48) }}" class="rounded-circle">
                                        </div>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"@if($user->trashed()) class="text-danger" @endif>{{ $user->name }}</a>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('Showing :from-:to of :total', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $users->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

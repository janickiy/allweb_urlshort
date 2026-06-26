@section('site_title', formatTitle([__('Edit'), __('Domain'), config('settings.title')]))

@if(! isset($admin))
    @include('shared.breadcrumbs', ['breadcrumbs' => [
        ['url' => route('dashboard'), 'title' => __('Home')],
        ['url' => route('domains'), 'title' => __('Domains')],
        ['title' => __('Edit')],
    ]])

    <div class="d-flex">
        <h2 class="mb-3 text-break">{{ __('Edit') }}</h2>
    </div>
@endif

<form action="{{ isset($admin) ? route('admin.domains.edit', $domain->id) : route('domains.edit', $domain->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="{{ isset($admin) ? 'card card-primary card-outline shadow-sm mb-0 admin-form-card admin-domain-edit-card' : 'card border-0 shadow-sm' }}">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    @if(isset($admin))
                        <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                            @include('icons.domain', ['class' => 'fill-current icon-text'])
                            {{ __('Domain') }}
                        </h3>
                    @else
                        <div class="font-weight-medium py-1">{{ __('Domain') }}</div>
                    @endif
                </div>

                @if(isset($admin))
                    <div class="col-12 col-md-auto">
                        <a href="{{ route('admin.links', ['domain_id' => $domain->id]) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                            @include('icons.link', ['class' => 'fill-current icon-button-sm'])
                            {{ __('Links') }}
                            <span class="badge text-bg-primary">{{ number_format($stats['links'], 0, __('.'), __(',')) }}</span>
                        </a>
                    </div>
                @else
                    <div class="col-auto">
                        <a href="{{ route('links', ['domain' => $domain->id]) }}" class="btn btn-outline-primary btn-sm">{{ __('View') }}</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            @include('shared.message')

            <div class="row g-3">
                <div class="{{ isset($admin) ? 'col-12 col-lg-6' : 'col-12' }}">
                    <div class="mb-3">
                        <label class="{{ isset($admin) ? 'form-label' : '' }}" for="i_name">{{ __('Domain') }}</label>
                        <input type="text" name="name" class="form-control" id="i_name" value="{{ str_replace(['http://', 'https://'], '', $domain->name) }}" disabled>
                    </div>
                </div>

                <div class="{{ isset($admin) ? 'col-12 col-lg-6' : 'col-12' }}">
                    <div class="mb-3">
                        <label class="{{ isset($admin) ? 'form-label' : '' }}" for="i_index_page">{{ __('Custom index') }}</label>
                        <input type="text" name="index_page" id="i_index_page" class="form-control{{ $errors->has('index_page') ? ' is-invalid' : '' }}" value="{{ $domain->index_page }}">
                        @if ($errors->has('index_page'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('index_page') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('Add a custom index page.') }}</small>
                    </div>
                </div>

                <div class="{{ isset($admin) ? 'col-12 col-lg-6' : 'col-12' }}">
                    <div class="mb-3">
                        <label class="{{ isset($admin) ? 'form-label' : '' }}" for="i_not_found_page">{{ __('Custom 404') }}</label>
                        <input type="text" name="not_found_page" id="i_not_found_page" class="form-control{{ $errors->has('not_found_page') ? ' is-invalid' : '' }}" value="{{ $domain->not_found_page }}">
                        @if ($errors->has('not_found_page'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('not_found_page') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('Add a custom 404 page.') }}</small>
                    </div>
                </div>
            </div>

            @if(! isset($admin))
                <div class="row mt-3">
                    <div class="col">
                        <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteModal">{{ __('Delete') }}</button>
                    </div>
                </div>
            @endif
        </div>

        @if(isset($admin))
            <div class="card-footer bg-body">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        @include('icons.delete', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Delete') }}
                    </button>
                    <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</form>

@if(isset($admin) && isset($domain->user))
    <div class="card card-secondary card-outline shadow-sm mt-3 admin-form-card admin-domain-user-card">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.user', ['class' => 'fill-current icon-text'])
                        {{ __('User') }}
                    </h3>
                </div>
                <div class="col-12 col-md-auto">
                    <a href="{{ route('admin.users.edit', $domain->user->id) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                        @include('icons.edit', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Edit') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Name') }}</div>
                    <div class="fw-medium">{{ $domain->user->name }}</div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Email') }}</div>
                    <div class="fw-medium">{{ $domain->user->email }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="deleteDomainModalLabel">{{ __('Delete') }}</h6>
                <button type="button" class="close d-flex align-items-center justify-content-center" @if(isset($admin)) data-bs-dismiss="modal" @else data-dismiss="modal" @endif aria-label="{{ __('Close') }}">
                    <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">{{ __('Deleting this domain is permanent, and will remove all the links associated with it.') }}</div>
                <div>{{ __('Are you sure you want to delete :name?', ['name' => str_replace(['http://', 'https://'], '', $domain->name)]) }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @if(isset($admin)) data-bs-dismiss="modal" @else data-dismiss="modal" @endif>{{ __('Close') }}</button>
                <form action="{{ isset($admin) ? route('admin.domains.delete', $domain->id) : route('domains.delete', $domain->id) }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

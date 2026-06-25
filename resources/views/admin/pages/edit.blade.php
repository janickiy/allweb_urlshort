@include('shared.message')

<form action="{{ route('admin.pages.edit', $page->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0 admin-form-card">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.page', ['class' => 'fill-current icon-text'])
                        {{ __('Page') }}
                    </h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('page', $page->slug) }}" class="btn btn-outline-primary btn-sm">{{ __('View') }}</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_title" class="form-label">{{ __('Title') }}</label>
                    <input type="text" name="title" id="i_title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title', $page->title) }}">
                    @if ($errors->has('title'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_slug" class="form-label">{{ __('Slug') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ str_replace(['http://', 'https://'], '', url('/page')) }}/</span>
                        <input type="text" name="slug" id="i_slug" class="form-control{{ $errors->has('slug') ? ' is-invalid' : '' }}" value="{{ old('slug', $page->slug) }}">
                    </div>
                    @if ($errors->has('slug'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('slug') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('Visibility') }}</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="footer" id="i_footer" value="1" @if(old('footer', $page->footer)) checked @endif>
                        <label class="form-check-label" for="i_footer">{{ __('Footer') }}</label>
                    </div>
                    @if ($errors->has('footer'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('footer') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-12">
                    <label for="i_content" class="form-label">{{ __('Content') }}</label>
                    <textarea name="content" id="i_content" class="form-control{{ $errors->has('content') ? ' is-invalid' : '' }}">{{ old('content', $page->content) }}</textarea>
                    @if ($errors->has('content'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('content') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer bg-body d-flex justify-content-between">
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">{{ __('Delete') }}</button>
            <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="deletePageModalLabel">{{ __('Delete') }}</h6>
                <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete :name?', ['name' => $page->title]) }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <form action="{{ route('admin.pages.delete', $page->id) }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

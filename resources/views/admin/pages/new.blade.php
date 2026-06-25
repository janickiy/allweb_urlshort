@include('shared.message')

<form action="{{ route('admin.pages.new') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0 admin-form-card">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.page', ['class' => 'fill-current icon-text'])
                {{ __('Page') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_title" class="form-label">{{ __('Title') }}</label>
                    <input type="text" name="title" id="i_title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title') }}">
                    @if ($errors->has('title'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_slug" class="form-label">{{ __('Slug') }}</label>
                    <input type="text" name="slug" id="i_slug" class="form-control{{ $errors->has('slug') ? ' is-invalid' : '' }}" value="{{ old('slug') }}">
                    @if ($errors->has('slug'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('slug') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('Visibility') }}</label>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="footer" id="i_footer" value="1" @if(old('footer')) checked @endif>
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
                    <textarea name="content" id="i_content" class="form-control{{ $errors->has('content') ? ' is-invalid' : '' }}">{{ old('content') }}</textarea>
                    @if ($errors->has('content'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('content') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer bg-body d-flex justify-content-end">
            <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>

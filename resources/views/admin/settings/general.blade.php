@include('shared.message')

<form action="{{ route('admin.settings.general.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.settings', ['class' => 'fill-current icon-text'])
                {{ __('General') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_title" class="form-label">{{ __('Title') }}</label>
                    <input type="text" name="title" id="i_title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title', config('settings.title')) }}">
                    @if ($errors->has('title'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_tagline" class="form-label">{{ __('Tagline') }}</label>
                    <input type="text" name="tagline" id="i_tagline" class="form-control{{ $errors->has('tagline') ? ' is-invalid' : '' }}" value="{{ old('tagline', config('settings.tagline')) }}">
                    @if ($errors->has('tagline'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tagline') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_index" class="form-label">{{ __('Custom index') }}</label>
                    <input type="text" name="index" id="i_index" class="form-control{{ $errors->has('index') ? ' is-invalid' : '' }}" value="{{ old('index', config('settings.index')) }}">
                    @if ($errors->has('index'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('index') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_timezone" class="form-label">{{ __('Timezone') }}</label>
                    <select name="timezone" id="i_timezone" class="form-select{{ $errors->has('timezone') ? ' is-invalid' : '' }}">
                        @foreach(timezone_identifiers_list() as $value)
                            <option value="{{ $value }}" @if (old('timezone', config('settings.timezone')) == $value) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('timezone'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('timezone') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-12">
                    <label for="i_tracking_code" class="form-label">{{ __('Tracking Code') }}</label>
                    <textarea name="tracking_code" id="i_tracking_code" class="form-control font-monospace" rows="6">{{ old('tracking_code', config('settings.tracking_code')) }}</textarea>
                    @if ($errors->has('tracking_code'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('tracking_code') }}</strong>
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

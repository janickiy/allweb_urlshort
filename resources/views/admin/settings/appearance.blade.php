@include('shared.message')

<form action="{{ route('admin.settings.appearance.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.preview', ['class' => 'fill-current icon-text'])
                {{ __('Appearance') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="i_logo" class="form-label">{{ __('Logo') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body admin-upload-preview">
                            <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}" alt="{{ __('Logo') }}">
                        </span>
                        <input type="file" name="logo" id="i_logo" class="form-control{{ $errors->has('logo') ? ' is-invalid' : '' }}" accept=".jpg,.jpeg,.png,.bmp,.gif,.svg,.webp">
                    </div>
                    @if ($errors->has('logo'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('logo') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-lg-6">
                    <label for="i_favicon" class="form-label">{{ __('Favicon') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body admin-upload-preview">
                            <img src="{{ url('/') }}/uploads/brand/{{ config('settings.favicon') }}" alt="{{ __('Favicon') }}">
                        </span>
                        <input type="file" name="favicon" id="i_favicon" class="form-control{{ $errors->has('favicon') ? ' is-invalid' : '' }}" accept=".jpg,.jpeg,.png,.bmp,.gif,.svg,.webp">
                    </div>
                    @if ($errors->has('favicon'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('favicon') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-lg-6">
                    <label for="i_theme" class="form-label">{{ __('Theme') }} ({{ __('Default') }})</label>
                    <select name="theme" id="i_theme" class="form-select{{ $errors->has('theme') ? ' is-invalid' : '' }}">
                        @foreach([0 => __('Light'), 1 => __('Dark')] as $key => $value)
                            <option value="{{ $key }}" @if(old('theme', config('settings.theme')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('theme'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('theme') }}</strong>
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

@include('shared.message')

<form action="{{ route('admin.settings.captcha.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.security', ['class' => 'fill-current icon-text'])
                {{ __('Captcha') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_captcha_site_key" class="form-label">{{ __('reCAPTCHA site key') }}</label>
                    <input id="i_captcha_site_key" type="text" class="form-control{{ $errors->has('captcha_site_key') ? ' is-invalid' : '' }}" name="captcha_site_key" value="{{ old('captcha_site_key', config('settings.captcha_site_key')) }}">
                    @if ($errors->has('captcha_site_key'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('captcha_site_key') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_captcha_secret_key" class="form-label">{{ __('reCAPTCHA secret key') }}</label>
                    <input id="i_captcha_secret_key" type="password" class="form-control{{ $errors->has('captcha_secret_key') ? ' is-invalid' : '' }}" name="captcha_secret_key" value="{{ old('captcha_secret_key', config('settings.captcha_secret_key')) }}">
                    @if ($errors->has('captcha_secret_key'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('captcha_secret_key') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-4">
                    <label for="i_captcha_shorten" class="form-label">{{ Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Shorten')]))) }}</label>
                    <select name="captcha_shorten" id="i_captcha_shorten" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('captcha_shorten', config('settings.captcha_shorten')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="i_captcha_registration" class="form-label">{{ Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Registration')]))) }}</label>
                    <select name="captcha_registration" id="i_captcha_registration" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('captcha_registration', config('settings.captcha_registration')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="i_captcha_contact" class="form-label">{{ Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Contact')]))) }}</label>
                    <select name="captcha_contact" id="i_captcha_contact" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('captcha_contact', config('settings.captcha_contact')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
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

@include('shared.message')

<form action="{{ route('admin.settings.registration.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.users', ['class' => 'fill-current icon-text'])
                {{ __('Registration') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_registration_registration" class="form-label">{{ __('Registration') }}</label>
                    <select name="registration_registration" id="i_registration_registration" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('registration_registration', config('settings.registration_registration')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="i_registration_verification" class="form-label">{{ __('Email verification') }}</label>
                    <select name="registration_verification" id="i_registration_verification" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('registration_verification', config('settings.registration_verification')) == $key) selected @endif>{{ $value }}</option>
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

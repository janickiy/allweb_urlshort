@include('shared.message')

<form action="{{ route('admin.settings.email.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.email', ['class' => 'fill-current icon-text'])
                {{ __('Email') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_email_driver" class="form-label">{{ __('Driver') }}</label>
                    <select name="email_driver" id="i_email_driver" class="form-select">
                        @foreach(['smtp', 'log'] as $value)
                            <option value="{{ $value }}" @if (old('email_driver', config('settings.email_driver')) == $value) selected @endif>{{ ucfirst($value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="i_email_encryption" class="form-label">{{ __('Encryption') }}</label>
                    <select name="email_encryption" id="i_email_encryption" class="form-select">
                        @foreach(['' => __('None'), 'tls' => 'TLS', 'ssl' => 'SSL'] as $value => $label)
                            <option value="{{ $value }}" @if (old('email_encryption', config('settings.email_encryption')) == $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="i_email_host" class="form-label">{{ __('Host') }}</label>
                    <input type="text" name="email_host" id="i_email_host" class="form-control" value="{{ old('email_host', config('settings.email_host')) }}">
                </div>

                <div class="col-md-4">
                    <label for="i_email_port" class="form-label">{{ __('Port') }}</label>
                    <input type="number" name="email_port" id="i_email_port" class="form-control" value="{{ old('email_port', config('settings.email_port')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_email_address" class="form-label">{{ __('Email address') }}</label>
                    <input type="email" name="email_address" id="i_email_address" class="form-control" value="{{ old('email_address', config('settings.email_address')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_email_username" class="form-label">{{ __('Username') }}</label>
                    <input type="text" name="email_username" id="i_email_username" class="form-control" value="{{ old('email_username', config('settings.email_username')) }}">
                </div>

                <div class="col-12">
                    <label for="i_email_password" class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="email_password" id="i_email_password" class="form-control" value="{{ old('email_password', config('settings.email_password')) }}">
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

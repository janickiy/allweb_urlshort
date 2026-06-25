<form action="{{ route('admin.languages.new') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0 admin-form-card">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.language', ['class' => 'fill-current icon-text'])
                {{ __('Language') }}
            </h3>
        </div>

        <div class="card-body">
            <label for="i_language" class="form-label">{{ __('Language') }}</label>
            <input type="file" name="language" id="i_language" class="form-control{{ $errors->has('language') ? ' is-invalid' : '' }}" accept=".json">
            @if ($errors->has('language'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('language') }}</strong>
                </span>
            @endif
        </div>

        <div class="card-footer bg-body d-flex justify-content-end">
            <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>

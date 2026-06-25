@include('shared.message')

<form action="{{ route('admin.settings.legal.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.page', ['class' => 'fill-current icon-text'])
                {{ __('Legal') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_legal_terms_url" class="form-label">{{ __(':name URL', ['name' => __('Terms of Service')]) }}</label>
                    <input type="text" name="legal_terms_url" id="i_legal_terms_url" class="form-control" value="{{ old('legal_terms_url', config('settings.legal_terms_url')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_privacy_url" class="form-label">{{ __(':name URL', ['name' => __('Privacy Policy')]) }}</label>
                    <input type="text" name="legal_privacy_url" id="i_privacy_url" class="form-control" value="{{ old('legal_privacy_url', config('settings.legal_privacy_url')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_cookie_url" class="form-label">{{ __(':name URL', ['name' => __('Cookie Policy')]) }}</label>
                    <input type="text" name="legal_cookie_url" id="i_cookie_url" class="form-control" value="{{ old('legal_cookie_url', config('settings.legal_cookie_url')) }}">
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

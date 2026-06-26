@include('shared.message')

<form action="{{ route('admin.settings.social.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.social', ['class' => 'fill-current icon-text'])
                {{ __('Social') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_social_facebook" class="form-label">{{ __('Facebook') }}</label>
                    <input type="text" name="social_facebook" id="i_social_facebook" class="form-control" value="{{ old('social_facebook', config('settings.social_facebook')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_social_twitter" class="form-label">{{ __('X') }}</label>
                    <input type="text" name="social_twitter" id="i_social_twitter" class="form-control" value="{{ old('social_twitter', config('settings.social_twitter')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_social_instagram" class="form-label">{{ __('Instagram') }}</label>
                    <input type="text" name="social_instagram" id="i_social_instagram" class="form-control" value="{{ old('social_instagram', config('settings.social_instagram')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_social_youtube" class="form-label">{{ __('YouTube') }}</label>
                    <input type="text" name="social_youtube" id="i_social_youtube" class="form-control" value="{{ old('social_youtube', config('settings.social_youtube')) }}">
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

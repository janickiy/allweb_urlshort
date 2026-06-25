@include('shared.message')

<form action="{{ route('admin.settings.shortener.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.link', ['class' => 'fill-current icon-text'])
                {{ __('Shortener') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_short_guest" class="form-label">{{ __('Guest shortening') }}</label>
                    <select name="short_guest" id="i_short_guest" class="form-select">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if (old('short_guest', config('settings.short_guest')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label for="i_short_bad_words" class="form-label">{{ __('Bad words') }}</label>
                    <textarea name="short_bad_words" id="i_short_bad_words" class="form-control" rows="4">{{ old('short_bad_words', config('settings.short_bad_words')) }}</textarea>
                    <div class="form-text">{{ __('New row acts as a delimiter.') }}</div>
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

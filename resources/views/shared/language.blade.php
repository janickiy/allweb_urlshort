@if(count(config('app.locales')) > 1)
    @php
        $currentLocale = app()->getLocale();
        $currentLocaleName = config('app.locales')[$currentLocale] ?? strtoupper($currentLocale);
        $languageWrapperClass = $languageWrapperClass ?? 'd-block d-md-inline-flex ' . (__('lang_dir') == 'rtl' ? ' mr-lg-3' : ' ml-lg-3');
        $languageLinkClass = $languageLinkClass ?? 'text-secondary text-decoration-none d-flex align-items-center py-1';
    @endphp

    <div class="{{ $languageWrapperClass }}" data-toggle="tooltip" title="{{ __('ui.actions.change_language') }}">
        <a href="#" class="{{ $languageLinkClass }}" data-toggle="modal" data-target="#changeLanguage">
            <span class="d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">@include('icons/language', ['class' => 'icon-text fill-current'])</span>
            <span class="flex-grow-1"><span class="text-muted">{{ $currentLocaleName }}</span></span>
        </a>
    </div>

    <div class="modal fade" id="changeLanguage" tabindex="-1" role="dialog" aria-labelledby="{{ __('ui.actions.change_language') }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('ui.actions.change_language') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <form action="{{ route('locale') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            @foreach(config('app.locales') as $code => $name)
                                <div class="col-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="i_language_{{ $code }}" name="locale" class="custom-control-input" value="{{ $code }}" @if($currentLocale === $code) checked @endif>
                                        <label class="custom-control-label" for="i_language_{{ $code }}" lang="{{ $code }}">{{ $name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('ui.actions.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('ui.actions.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

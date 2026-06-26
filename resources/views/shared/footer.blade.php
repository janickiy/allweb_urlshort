<footer id="footer" class="footer bg-base-0{{ isset($lightweight) ? ' d-print-none' : '' }}">
    <div class="container py-5">
        @if(isset($lightweight) == false)
            <div class="row">
                <div class="col-12 col-lg">
                    <ul class="nav p-0 mx-n3 mb-3 mb-lg-0 d-flex flex-column flex-lg-row">
                        <li class="nav-item">
                            <a href="{{ route('contact') }}" class="nav-link py-1">{{ __('Contact') }}</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('developers') }}" class="nav-link py-1">{{ __('Developers') }}</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ config('settings.legal_terms_url') }}" class="nav-link py-1">{{ __('Terms') }}</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ config('settings.legal_privacy_url') }}" class="nav-link py-1">{{ __('Privacy') }}</a>
                        </li>

                        @foreach ($footerPages as $page)
                            <li class="nav-item">
                                <a href="{{ route('page', $page['slug']) }}" class="nav-link py-1">{{ __($page['title']) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <hr>
        @endif
        <div class="row">
            <div class="col-12 col-lg order-2 order-lg-1">
                <div class="text-muted py-1">
                    {{ __('© :year :name.', ['year' => now()->year, 'name' => config('settings.title')]) }} {{ __('All rights reserved.') }}
                    <a href="{{ config('info.software.url') }}" target="_blank" rel="noopener">{{ config('info.software.author') }}</a>
                </div>
            </div>
        </div>
    </div>

    @include('shared.cookie_law')
</footer>

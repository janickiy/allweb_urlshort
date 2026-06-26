@section('site_title', formatTitle([config('settings.title'), e(config('settings.tagline'))]))

@extends('layouts.app')

@section('content')
    <div class="flex-fill">
    <div class="bg-base-0 position-relative">
        <div class="container position-relative py-5 py-sm-6">
            <div class="row">
                <div class="col-12 col-lg-6 pt-sm-5">
                    <h1 class="display-4 font-weight-medium">{{ __('More than a URL shortener.') }}</h1>
                    <p class="text-muted font-weight-normal mt-4 font-size-lg">{{ __('Create, manage, track and optimize every link from one powerful platform.') }}</p>

                    @if(config('settings.short_guest'))
                        <div class="form-group mt-5" id="short-form-container"@if(session('link')) style="display: none;"@endif>
                            <form action="{{ route('guest') }}" method="post" enctype="multipart/form-data" id="short-form">
                                @csrf
                                <div class="form-row">
                                    <div class="col-12 col-sm">
                                        <input type="text" autocomplete="off" autocapitalize="none" spellcheck="false" name="url" class="form-control form-control-lg font-size-lg{{ $errors->has('url') ? ' is-invalid' : '' }}" placeholder="{{ __('Shorten your link') }}">
                                        @if ($errors->has('url'))
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $errors->first('url') }}</strong>
                                            </span>
                                        @endif

                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-12 col-sm-auto">
                                        @if(config('settings.captcha_shorten'))
                                            {!! NoCaptcha::displaySubmit('short-form', __('Shorten'), ['data-theme' => (Cookie::get('dark_mode') == 1 ? 'dark' : 'light'), 'data-size' => 'invisible', 'class' => 'btn btn-primary btn-lg btn-block font-size-lg mt-3 mt-sm-0']) !!}

                                            {!! NoCaptcha::renderJs(__('lang_code')) !!}
                                        @else
                                            <button class="btn btn-primary btn-lg btn-block font-size-lg mt-3 mt-sm-0" type="submit">{{ __('Shorten') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('home.link')
                    @else
                        <div class="form-group mt-5">
                            @if(config('settings.registration_registration'))
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg font-size-lg{{ (__('lang_dir') == 'rtl' ? ' ml-3' : ' mr-3') }}">{{ __('Get started for free') }}</a>
                            @endif
                            <a href="#features" class="btn btn-outline-primary btn-lg font-size-lg" data-scroll-to="72">{{ __('Learn more') }}</a>
                        </div>
                    @endif
                </div>

                <div class="col-6 d-none d-lg-block position-relative {{ (__('lang_dir') == 'rtl' ? 'pr-5' : 'pl-5') }}">
                    @include('home.illustrations.hero')
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-1" id="features">
        <div class="container py-5 py-md-7">
            <div class="text-center">
                <h2 class="mb-3 d-inline-block">{{ __('Features') }}</h2>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg">{{ __('Professional link management with branded domains, analytics, smart targeting, and campaign organization.') }}</p>
                </div>
            </div>

            <div class="row mx-lg-n4">
                @php
                    $features = [
                        [
                            'icon' => 'stats',
                            'title' => __('Statistics'),
                            'description' => __('Gain valuable insights into your audience with detailed analytics, including clicks, locations, devices, browsers, platforms, and traffic sources.')
                        ],
                        [
                            'icon' => 'geolocation',
                            'title' => __('Geotargeting'),
                            'description' => __('Deliver visitors to the most relevant destination based on their country or region, improving user experience and campaign performance.')
                        ],
                        [
                            'icon' => 'account',
                            'title' => __('Personal'),
                            'description' => __('Create short, branded links that are recognizable, trustworthy, and easy to share.')
                        ],
                        [
                            'icon' => 'devices',
                            'title' => __('Platform targeting'),
                            'description' => __('Automatically redirect visitors to the most appropriate destination based on their device.')
                        ],
                        [
                            'icon' => 'campaign',
                            'title' => __('Campaigns'),
                            'description' => __('Create limited-time campaigns with automatic link expiration to keep your promotions accurate and up to date.')
                        ],
                        [
                            'icon' => 'privacy',
                            'title' => __('Privacy'),
                            'description' => __('Protect sensitive links with password access and ensure only authorized users can view your content.')
                        ],
                    ];
                @endphp

                @foreach($features as $feature)
                    <div class="col-12 col-sm-6 col-md-4 pt-3 pr-md-3 pl-md-3 pt-lg-4 pr-lg-4 pl-lg-4 mt-4 feature">
                        <div class="d-flex flex-column">
                            <div class="icon-gradient-{{ $loop->index+1 }}">@include('icons.background.'.$feature['icon'], ['class' => 'fill-current mb-3 icon-features'])</div>
                            <div class="d-block w-100"><h5 class="mt-1 mb-3 d-inline-block">{{ $feature['title'] }}</h5></div>
                            <div class="d-block w-100 text-muted">{{ $feature['description'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $features2 = [
            [
                'icon' => 'domain',
                'title' => __('Custom Domains'),
                'color' => 'primary',
                'description' => __('Use your own domain name to create professional short links that reinforce your brand identity and improve user confidence.')
            ],
            [
                'icon' => 'alias',
                'title' => __('Custom Aliases'),
                'color' => 'primary',
                'description' => __('Create memorable, human-friendly URLs instead of random characters, making your links easier to recognize, share, and remembe')
            ]
        ];
    @endphp

    <div class="bg-base-0">
        <div class="container py-5 py-md-7 position-relative z-1">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h2 class="mb-3">{{ __('Build Trusted Branded Links') }}</h2>
                    <div class="m-auto">
                        <p class="text-muted font-weight-normal font-size-lg">{{ __('Your links are often the first thing people see. Branded short links help increase brand recognition, build trust, and encourage more clicks across every marketing channel.') }}</p>
                    </div>
                    <div class="text-muted my-4">⸻</div>

                    <div class="row mx-lg-n4">
                        @foreach($features2 as $feature)
                            <div class="col-12 pt-3 pr-md-3 pl-md-3 pt-lg-4 pr-lg-4 pl-lg-4 mt-4 feature">
                                <div class="d-flex flex-row">
                                    <div class="text-{{ $feature['color'] }} {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">@include('icons.background.'.$feature['icon'], ['class' => 'fill-current icon-features'])</div>
                                    <div class="{{ (__('lang_dir') == 'rtl' ? 'mr-1' : 'ml-1') }}">
                                        <div class="d-block w-100"><h5 class="mt-0 mb-1 d-inline-block">{{ $feature['title'] }}</h5></div>
                                        <div class="d-block w-100 text-muted">{{ $feature['description'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-6 d-none d-lg-block position-relative">
                    @include('home.illustrations.links')
                </div>
            </div>
        </div>
    </div>

    @if(config('settings.stripe'))
    <div class="bg-base-1">
        <div class="container py-5 py-md-7 position-relative z-1">
            <div class="text-center">
                <h2 class="mb-3 d-inline-block">{{ __('Plans') }}</h2>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg">{{ __('Simple pricing plans for everyone and every budget.') }}</p>
                </div>
            </div>

            <div class="row mx-lg-n4 justify-content-center position-relative">

                <svg class="position-absolute d-none d-md-block text-danger" style="width: 5.5rem; height: 5.5rem; top: 1.5rem; {{ (__('lang_dir') == 'rtl' ? 'right: 0;' : 'left: 0;') }}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 110 110"><defs><pattern id="dots3" width="20" height="20" patternUnits="userSpaceOnUse" fill="currentColor" viewBox="0 0 20 20"><rect width="20" height="20" style="fill:none"/><circle cx="5" cy="5" r="3"/></pattern></defs><rect width="110" height="110" style="opacity:0.3;fill:url(#dots3)"/></svg>

                @foreach($plans as $plan)
                    <div class="col-12 col-md-4 pt-md-3 pr-md-3 pl-md-3 pt-lg-4 pr-lg-4 pl-lg-4 mt-4 @if($plan->plan_month && $plan->plan_year) order-{{ $loop->remaining }} order-md-{{ $loop->iteration }} @else order-{{ $loop->remaining }} order-md-{{ $loop->iteration }} @endif">
                        <div class="card border-0 shadow-sm hover:shadow- transition-box-shadow duration-300 rounded h-100 overflow-hidden plan">
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="mt-1 mb-3 text-muted text-uppercase d-inline-block">{{ $plan->name }}</h5>

                                <div class="plan-title-underline" style="background-color: {{ $plan->color }};"></div>

                                <div class="mt-4">
                                    {{ __($plan->description) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                <a href="{{ route('pricing') }}" class="btn btn-primary py-2 mt-5">{{ __('View pricing') }}</a>
            </div>
        </div>
    </div>

    <div class="bg-base-0">
        <div class="container position-relative text-center py-5 py-md-7 d-flex flex-column z-1">
            <div class="flex-grow-1">
                <div class="badge badge-pill badge-success mb-3 px-3 py-2">{{ __('Join us') }}</div>
                <div class="text-center">
                    <h4 class="mb-3">{{ __('Ready to get started?') }}</h4>
                    <div class="m-auto">
                        <p class="mb-5 font-weight-normal text-muted font-size-lg">{{ __('Reach your customers more efficiently by starting your marketing campaign with us.') }}</p>
                    </div>
                </div>
            </div>

            <div><a href="{{ route('register') }}" class="btn btn-primary py-2">{{ __('Get started') }}</a></div>
        </div>
    </div>
    @endif

    <div class="bg-base-1">
        <div class="container pt-6">
            @php
                $counters = [
                    [
                        'value' => number_format($stats['links'], 0, __('.'), __(',')),
                        'title' => __('Created links'),
                        'id' => 'links_created'
                    ],
                    [
                        'value' => number_format($stats['redirects'], 0, __('.'), __(',')),
                        'title' => __('Redirected links'),
                        'id' => 'links_redirected'
                    ],
                    [
                        'value' => number_format($stats['users'], 0, __('.'), __(',')),
                        'title' => __('Registered users'),
                        'id' => 'users_registered'
                    ],
                ];
            @endphp

            <div class="row">
                @foreach($counters as $count)
                    <div class="col-12 col-md-4 mb-6 text-md-center">
                        <div class="display-4 font-weight-normal mb-1" id="{{ $count['id'] }}">{{ $count['value'] }}</div>
                        <div class="text-muted">{{ $count['title'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

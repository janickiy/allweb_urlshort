@extends('layouts.auth')

@section('site_title', formatTitle([__('Register'), config('settings.title')]))

@section('content')
<div class="bg-base-1 d-flex align-items-center flex-fill">
    <div class="container h-100 py-3 my-3">

        <div class="text-center"><h2 class="mb-3 d-inline-block d-block d-lg-none">{{ __('Register') }}</h2></div>

        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-12">
                <div class="card border-0 shadow-sm my-3 overflow-hidden">
                    <div class="row no-gutters">
                        <div class="col-12 col-lg-5">
                            <div class="card-body p-lg-5">
                                <a href="{{ route('home') }}" aria-label="{{ config('settings.title') }}" class="navbar-brand p-0 mb-4 d-none d-lg-flex align-items-center text-dark text-decoration-none">
                                    <div class="logo">
                                        <img src="{{ url('/') }}/uploads/brand/{{ config('settings.logo') }}">
                                    </div>
                                    <span class="font-weight-bold text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}">{{ config('settings.title') ?: config('info.software.name') }}</span>
                                </a>

                                <form method="POST" action="{{ route('register') }}" id="registration-form" autocomplete="off">
                                    @csrf

                                    <div class="form-group">
                                        <label for="i_name">{{ __('Name') }}</label>
                                        <input id="i_name" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" autocomplete="new-name" autofocus>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i_email">{{ __('Email address') }}</label>
                                        <input id="i_email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autocomplete="new-email">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i_password">{{ __('Password') }}</label>
                                        <input id="i_password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" autocomplete="new-password">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i_password_confirmation">{{ __('Confirm password') }}</label>
                                        <input id="i_password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" autocomplete="new-password">
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input{{ $errors->has('agreement') ? ' is-invalid' : '' }}" name="agreement" id="i_agreement">
                                            <label class="custom-control-label" for="i_agreement">{!! __('I agree to the :tos and :pp.', ['tos' => '<a href="'.config('settings.legal_terms_url').'" target="_blank">'. __('Terms of Service').'</a>', 'pp' => '<a href="'.config('settings.legal_privacy_url').'" target="_blank">'. __('Privacy Policy') .'</a>']) !!}</label>
                                            @if ($errors->has('agreement'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('agreement') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(config('settings.captcha_registration'))
                                        @include('shared.recaptcha_v3', ['formId' => 'registration-form', 'action' => 'register'])
                                    @endif
                                    <button type="submit" class="btn btn-block btn-primary py-2">
                                        {{ __('Register') }}
                                    </button>

                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                    @endif
                                </form>
                            </div>
                            <div class="card-footer bg-base-2 border-0">
                                <div class="text-center text-muted my-2">{{ __('Already have an account?') }} <a href="{{ route('login') }}" role="button">{{ __('Login') }}</a></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 bg-dark background-auth d-none d-lg-flex flex-fill bg-cover" style="background-image: url({{ asset('images/register-shortlink-pro.png') }}); background-position: center center;">
                            <div class="card-body p-lg-5 d-flex flex-column flex-fill bg-auth position-absolute" style="top: 0; right: 0; bottom: 0; left: 0">
                                <div class="d-flex align-items-center d-flex flex-fill">
                                    <div class="text-white-important {{ (__('lang_dir') == 'rtl' ? 'mr-5' : 'ml-5') }}">
                                        <div class="h2 font-weight-bold d-none d-lg-block">
                                            {{ __('Register') }}
                                        </div>
                                        <div class="font-size-lg font-weight-medium">
                                            {{ __('Join us') }} — {{ config('settings.title') ?: config('info.software.name') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

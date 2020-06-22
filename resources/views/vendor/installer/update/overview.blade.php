@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Update'), config('settings.title')]))

@section('content')

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body text-center py-5">
            <div class="my-6">
                <p class="text-muted font-size-lg">{{ __('Updates pending') }}</p>

                <div class="h1">{{ $numberOfUpdatesPending }}</div>
            </div>
        </div>
    </div>

    <a href="{{ route('LaravelUpdater::database') }}" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
        <span class="d-inline-flex align-items-center mx-auto">
            {{ __('Update') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_right' : 'icons.chevron_left'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
        </span>
    </a>

@endsection

@include('vendor.installer.update.menu')
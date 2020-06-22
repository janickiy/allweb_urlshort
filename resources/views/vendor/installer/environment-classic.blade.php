@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

@section('content')

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Settings') }}</div>
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
                {!! csrf_field() !!}
                <div class="form-group">
                    <textarea class="form-control" id="exampleFormControlTextarea1" name="envConfig" rows="5">{{ $envConfig }}</textarea>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>
                    {!! trans('installer_messages.environment.classic.save') !!}
                </button>
            </form>
        </div>
    </div>

    @if( ! isset($environment['errors']))
        <a href="{{ route('LaravelInstaller::database') }}" class="btn btn-primary btn-block mt-3 py-2">
            <span class="d-inline-flex align-items-center mx-auto">
                {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
            </span>
        </a>
    @endif

@endsection

@include('vendor.installer.menu')
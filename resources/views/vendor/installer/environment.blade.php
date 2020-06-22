@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

@section('container')

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Settings') }}</div>
        </div>
        <div class="card-body">
            <p class="text-center">
                {!! trans('installer_messages.environment.menu.desc') !!}
            </p>

            <a href="{{ route('LaravelInstaller::environmentWizard') }}" class="btn btn-primary">
                {{ trans('installer_messages.environment.menu.wizard-button') }}
            </a>
            <a href="{{ route('LaravelInstaller::environmentClassic') }}" class="btn btn-primary">
                {{ trans('installer_messages.environment.menu.classic-button') }}
            </a>
        </div>
    </div>

@endsection

@include('vendor.installer.menu')
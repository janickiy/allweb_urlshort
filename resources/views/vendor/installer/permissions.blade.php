@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Permissions') }}</div>
        </div>

        <div class="card-body">
            <div class="list-group list-group-flush my-n3">
                @foreach($permissions['permissions'] as $permission)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                {{ $permission['folder'] }}
                            </div>

                            <div class="col-auto d-flex align-items-center">
                                <span class="{{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                    {{ $permission['permission'] }}
                                </span>

                                @if($permission['isSet'])
                                    @include('icons/checkmark', ['class' => 'text-success icon-text fill-current'])
                                @else
                                    @include('icons/close', ['class' => 'text-danger icon-text fill-current pt-1'])
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ( ! isset($permissions['errors']))
        <a href="{{ route('LaravelInstaller::environmentWizard') }}" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
            <span class="d-inline-flex align-items-center mx-auto">
                {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
            </span>
        </a>
    @endif

@endsection

@include('vendor.installer.menu')
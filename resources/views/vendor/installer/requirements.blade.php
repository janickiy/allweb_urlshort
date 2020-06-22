@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Requirements') }}</div>
        </div>

        <div class="card-body">
            @foreach($requirements['requirements'] as $type => $requirement)
                <div class="list-group list-group-flush {{ $loop->index == 0 ? 'mb-n3 mt-n3' : 'mt-3 mb-n3 pt-3' }}">
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="font-weight-medium">{{ mb_strtoupper($type) }}</span>
                                @if($type == 'php')
                                    >= {{ $phpSupportInfo['minimum'] }}
                                @endif
                            </div>

                            <div class="col-auto d-flex align-items-center">
                                @if($type == 'php')
                                    <span class="{{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                        {{ $phpSupportInfo['current'] }}
                                    </span>
                                    @if($phpSupportInfo['supported'])
                                        @include('icons/checkmark', ['class' => 'text-success icon-text fill-current'])
                                    @else
                                        @include('icons/close', ['class' => 'text-danger icon-text fill-current pt-1'])
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    @foreach($requirements['requirements'][$type] as $extension => $enabled)
                        <div class="list-group-item px-0 text-muted">
                            <div class="row align-items-center">
                                <div class="col">
                                    {{ $extension }}
                                </div>
                                <div class="col-auto d-flex align-items-center">
                                    @if($enabled)
                                        @include('icons/checkmark', ['class' => 'text-success icon-text fill-current'])
                                    @else
                                        @include('icons/close', ['class' => 'text-danger icon-text fill-current pt-1'])
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    @if ( ! isset($requirements['errors']) && $phpSupportInfo['supported'] )
        <a href="{{ route('LaravelInstaller::permissions') }}" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
            <span class="d-inline-flex align-items-center mx-auto">
                {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
            </span>
        </a>
    @endif
@endsection

@include('vendor.installer.menu')
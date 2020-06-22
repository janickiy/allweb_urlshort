@extends('layouts.redirect')

@section('site_title', __('Link expired'))

@section('content')
<div class="bg-base-1 d-flex align-items-center flex-fill">
    <div class="container">
        <div class="row h-100 justify-content-center align-items-center py-3">
            <div class="col-lg-12">
                <h2 class="mb-5 text-center">{{ __('Link preview') }}</h2>
                <p class="mb-0 text-center text-break">{{ str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))) }}</p>
                <p class="my-2 text-center text-muted">{{ mb_strtolower(__('Redirects to')) }}</p>
                <p class="mb-5 text-center text-break">{{ $link->url }}</p>

                <div class="text-center">
                    <a href="{{ url()->previous() }}" class="btn btn-primary">{{ __('Go back') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
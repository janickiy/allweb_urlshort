@php
    $isAdminFlash = isset($admin) || request()->is('admin*');
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show{{ $isAdminFlash ? ' admin-flash-message' : '' }}" role="alert">
        <span class="{{ $isAdminFlash ? 'admin-flash-message-text' : '' }}">{{ session('success') }}</span>
        @if($isAdminFlash)
            <button type="button" class="btn-close admin-flash-message-close" data-bs-dismiss="alert" aria-label="{{ __('ui.actions.close') }}"></button>
        @else
            <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
            </button>
        @endif
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show{{ $isAdminFlash ? ' admin-flash-message' : '' }}" role="alert">
        <span class="{{ $isAdminFlash ? 'admin-flash-message-text' : '' }}">{{ session('error') }}</span>
        @if($isAdminFlash)
            <button type="button" class="btn-close admin-flash-message-close" data-bs-dismiss="alert" aria-label="{{ __('ui.actions.close') }}"></button>
        @else
            <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
            </button>
        @endif
    </div>
@endif

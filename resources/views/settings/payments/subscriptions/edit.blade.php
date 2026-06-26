@section('site_title', formatTitle([__('Edit'), __('Subscription'), config('settings.title')]))

@if(! isset($admin))
    @include('shared.breadcrumbs', ['breadcrumbs' => [
        ['url' => route('dashboard'), 'title' => __('Home')],
        ['url' => route('settings'), 'title' => __('Settings')],
        ['url' => route('settings.payments.subscriptions'), 'title' => __('Subscriptions')],
        ['title' => __('Edit')]
    ]])

    <h2 class="mb-0 d-inline-block">{{ __('Edit') }}</h2>
@endif

@php
    $stripeState = formatStripeStatus()[$subscription->stripe_status] ?? ['status' => 'secondary', 'title' => ucfirst($subscription->stripe_status)];
    $isMonthly = $plan->plan_month == $subscription->stripe_plan;
    $periodLabel = $isMonthly ? __('Monthly') : __('Yearly');
    $billingDate = $subscription->updated_at ?: $subscription->created_at;
    $renewsOn = null;

    if (! $subscription->ends_at && $billingDate) {
        $renewsOn = $isMonthly ? $billingDate->copy()->addMonth(1) : $billingDate->copy()->addYears(1);
    }
@endphp

<div class="{{ isset($admin) ? 'card card-primary card-outline shadow-sm mb-0 admin-form-card admin-subscription-edit-card' : 'card border-0 shadow-sm mt-3' }}">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                @if(isset($admin))
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.subscription', ['class' => 'fill-current icon-text'])
                        {{ __('Subscription') }}
                    </h3>
                @else
                    <div class="font-weight-medium py-1">{{ __('Subscription') }}</div>
                @endif
            </div>

            @if(isset($admin))
                <div class="col-12 col-md-auto">
                    <span class="badge badge-{{ $stripeState['status'] }}">{{ $stripeState['title'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="card-body{{ isset($admin) ? '' : ' mb-n3' }}">
        @include('shared.message')

        @if($subscription->stripe_status == 'active' && $subscription->ended())
            <div class="alert alert-danger" role="alert">
                {{ __(':name is cancelled.', ['name' => $subscription->name]) }}
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-6 mb-3">
                <div class="text-muted small mb-1">{{ __('Plan') }}</div>
                <div class="fw-medium">
                    {{ $subscription->name }}
                    <span class="badge badge-secondary">{{ $periodLabel }}</span>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-3">
                <div class="text-muted small mb-1">{{ __('Status') }}</div>
                <div><span class="badge badge-{{ $stripeState['status'] }}">{{ $stripeState['title'] }}</span></div>
            </div>

            @if(isset($admin))
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Plan ID') }}</div>
                    <div class="fw-medium text-break">{{ $subscription->stripe_plan ?: '-' }}</div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Subscription ID') }}</div>
                    <div class="fw-medium text-break">{{ $subscription->stripe_id ?: '-' }}</div>
                </div>
            @endif

            <div class="col-12 col-lg-6 mb-3">
                <div class="text-muted small mb-1">{{ __('Created at') }}</div>
                <div class="fw-medium">{{ $subscription->created_at->format(__('Y-m-d')) }}</div>
            </div>

            @if($subscription->updated_at)
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Updated at') }}</div>
                    <div class="fw-medium">{{ $subscription->updated_at->format(__('Y-m-d')) }}</div>
                </div>
            @endif

            @if($subscription->trial_ends_at)
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Trial ends at') }}</div>
                    <div class="fw-medium">{{ $subscription->trial_ends_at->format(__('Y-m-d')) }}</div>
                </div>
            @endif

            @if($subscription->ends_at)
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Ends at') }}</div>
                    <div class="fw-medium">{{ $subscription->ends_at->format(__('Y-m-d')) }}</div>
                </div>
            @elseif($renewsOn)
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted small mb-1">{{ __('Renews on') }}</div>
                    <div class="fw-medium">{{ $renewsOn->format(__('Y-m-d')) }}</div>
                </div>
            @endif
        </div>

        @if(isset($admin) == false)
            <div class="row">
                <div class="col">
                    @if($subscription->onGracePeriod() && $subscription->stripe_id)
                        <button type="button" class="btn btn-outline-success mb-3" data-toggle="modal" data-target="#resumeModal">{{ __('Resume') }}</button>
                    @endif

                    @if($subscription->hasIncompletePayment())
                        <a href="{{ route('checkout.confirm', $subscription->latestPayment()->id) }}" class="btn btn-outline-primary mb-3">{{ __('Confirm payment') }}</a>
                    @endif
                </div>

                <div class="col-auto">
                    @if($subscription->recurring() || ($subscription->onTrial() && !$subscription->onGracePeriod()))
                        <button type="button" class="btn btn-outline-danger mb-3" data-toggle="modal" data-target="#cancelModal">{{ __('Cancel') }}</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if(isset($admin) && ! $subscription->stripe_id)
        <div class="card-footer bg-body">
            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    @include('icons.delete', ['class' => 'fill-current icon-button-sm'])
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    @endif
</div>

@if(isset($admin))
    <div class="card card-secondary card-outline shadow-sm mt-3 admin-form-card admin-subscription-user-card">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.user', ['class' => 'fill-current icon-text'])
                        {{ __('User') }}
                    </h3>
                </div>
                <div class="col-12 col-md-auto">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                        @include('icons.edit', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Edit') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Name') }}</div>
                    <div class="fw-medium">{{ $user->name }}</div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Email') }}</div>
                    <div class="fw-medium">{{ $user->email }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(! $subscription->stripe_id)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h6 class="modal-title" id="deleteSubscriptionModalLabel">{{ __('Delete') }}</h6>
                        <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ __('Are you sure you want to delete :name?', ['name' => $subscription->name]) }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <form action="{{ route('admin.subscriptions.delete', $subscription->id) }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="cancelSubscriptionModalLabel">{{ __('Cancel') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">{{ __('You\'ll continue to have access to the features you\'ve paid for until the end of your billing cycle.') }}</div>
                    <div>{{ __('Are you sure you want to cancel your subscription?') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('settings.payments.subscriptions.cancel', ['subscription' => $subscription->name]) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <button type="submit" class="btn btn-danger">{{ __('Cancel') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resumeModal" tabindex="-1" role="dialog" aria-labelledby="resumeSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="resumeSubscriptionModalLabel">{{ __('Resume') }}</h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>{{ __('Are you sure you want to resume your subscription?') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <form action="{{ route('settings.payments.subscriptions.resume', ['subscription' => $subscription->name]) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <button type="submit" class="btn btn-success">{{ __('Resume') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

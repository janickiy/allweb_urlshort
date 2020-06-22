@section('site_title', formatTitle([__('Edit'), __('Payment methods'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('settings'), 'title' => __('Settings')],
    ['url' => route('settings.payments.methods'), 'title' => __('Payment methods')],
    ['title' => __('Edit')],
]])

<h2 class="mb-3 d-inline-block">{{ __('Edit') }}</h2>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="font-weight-medium py-1">{{ __('Payment method') }}</div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('settings.payments.methods.edit', $id) }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i_card_number">{{ __('Card number') }}</label>
                <div class="input-group flex-nowrap">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="addon-wrapping"><div class="d-flex align-items-center payment-icon">@include('icons.payments.' . (in_array($paymentMethod->card->brand, config('payments')) ? $paymentMethod->card->brand : 'unknown'))</div></span>
                    </div>
                    <input id="i_card_number" name="card-number" type="text" class="form-control" value="•••• {{ $paymentMethod->card->last4 }}" disabled>
                    <div class="input-group-append">
                        <span class="input-group-text"><span class="card-expiry">{{ date('m / y', strtotime('01-'.$paymentMethod->card->exp_month.'-'.$paymentMethod->card->exp_year)) }}</span></span>
                    </div>
                </div>
            </div>

            {{--<div class="form-group">
                <label for="i_address">{{ __('Address') }}</label>
                <input type="text" name="address" id="i_address" class="form-control" value="{{ $paymentMethod->billing_details->address->line1 }}">
            </div>

            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="i_city">{{ __('City') }}</label>
                        <input type="text" name="city" id="i_city" class="form-control" value="{{ $paymentMethod->billing_details->address->city }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="i_state">{{ __('State') }}</label>
                        <input type="text" name="state" id="i_state" class="form-control" value="{{ $paymentMethod->billing_details->address->state }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="i_postal_code">{{ __('Postal code') }}</label>
                        <input type="text" name="postal_code" id="i_postal_code" class="form-control" value="{{ $paymentMethod->billing_details->address->postal_code }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="i_country">{{ __('Country') }}</label>
                <select name="country" id="i_country" class="form-control">
                    <option value="" hidden disabled selected>{{ __('Country') }}</option>
                    @foreach(config('countries') as $key => $value)
                        <option value="{{ $key }}" @if ($key == $paymentMethod->billing_details->address->country) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
            </div>--}}

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="default" id="i_default" @if($defaultPaymentMethod->id == $paymentMethod->id) checked disabled @endif>
                    <label class="custom-control-label" for="i_default">{{ __('Default') }}</label>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteModal">{{ __('Delete') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">{{ __('Delete') }}</h6>
                <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete :name?', ['name' => $paymentMethod->card->last4]) }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <form action="{{ route('settings.payments.methods.delete', $id) }}" method="post" enctype="multipart/form-data">

                    @csrf

                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('shared.message')

<form action="{{ route('admin.settings.payment.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.subscription', ['class' => 'fill-current icon-text'])
                {{ __('Payment') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="i_stripe" class="form-label">{{ __('Enabled') }}</label>
                    <select name="stripe" id="i_stripe" class="form-select{{ $errors->has('stripe') ? ' is-invalid' : '' }}">
                        @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                            <option value="{{ $key }}" @if(old('stripe', config('settings.stripe')) == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('stripe'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('stripe') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-8">
                    <label for="i_stripe_wh_url" class="form-label">{{ __('Stripe webhook URL') }}</label>
                    <input type="text" name="stripe_wh_url" id="i_stripe_wh_url" class="form-control" value="{{ route('stripe.webhook') }}" disabled>
                </div>

                <div class="col-12">
                    <label for="i_stripe_key" class="form-label">{{ __('Stripe publishable key') }}</label>
                    <input type="text" name="stripe_key" id="i_stripe_key" class="form-control{{ $errors->has('stripe_key') ? ' is-invalid' : '' }}" value="{{ old('stripe_key', config('settings.stripe_key')) }}">
                    @if ($errors->has('stripe_key'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('stripe_key') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_stripe_secret" class="form-label">{{ __('Stripe secret key') }}</label>
                    <input type="password" name="stripe_secret" id="i_stripe_secret" class="form-control{{ $errors->has('stripe_secret') ? ' is-invalid' : '' }}" value="{{ old('stripe_secret', config('settings.stripe_secret')) }}">
                    @if ($errors->has('stripe_secret'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('stripe_secret') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="i_stripe_wh_secret" class="form-label">{{ __('Stripe webhook secret key') }}</label>
                    <input type="password" name="stripe_wh_secret" id="i_stripe_wh_secret" class="form-control{{ $errors->has('stripe_wh_secret') ? ' is-invalid' : '' }}" value="{{ old('stripe_wh_secret', config('settings.stripe_wh_secret')) }}">
                    @if ($errors->has('stripe_wh_secret'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('stripe_wh_secret') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer bg-body d-flex justify-content-end">
            <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>

@include('shared.message')

<form action="{{ route('admin.settings.invoice.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                @include('icons.subscription', ['class' => 'fill-current icon-text'])
                {{ __('Invoice') }}
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="i_invoice_vendor" class="form-label">{{ __('Vendor') }}</label>
                    <input type="text" name="invoice_vendor" id="i_invoice_vendor" class="form-control" value="{{ old('invoice_vendor', config('settings.invoice_vendor')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_invoice_country" class="form-label">{{ __('Country') }}</label>
                    <select name="invoice_country" id="i_invoice_country" class="form-select">
                        <option value="" hidden disabled @if (!old('invoice_country', config('settings.invoice_country'))) selected @endif>{{ __('Country') }}</option>
                        @foreach(config('countries') as $key => $value)
                            <option value="{{ $key }}" @if ($key == old('invoice_country', config('settings.invoice_country'))) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label for="i_invoice_address" class="form-label">{{ __('Address') }}</label>
                    <input type="text" name="invoice_address" id="i_invoice_address" class="form-control" value="{{ old('invoice_address', config('settings.invoice_address')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_invoice_city" class="form-label">{{ __('City') }}</label>
                    <input type="text" name="invoice_city" id="i_invoice_city" class="form-control" value="{{ old('invoice_city', config('settings.invoice_city')) }}">
                </div>

                <div class="col-md-3">
                    <label for="i_invoice_state" class="form-label">{{ __('State') }}</label>
                    <input type="text" name="invoice_state" id="i_invoice_state" class="form-control" value="{{ old('invoice_state', config('settings.invoice_state')) }}">
                </div>

                <div class="col-md-3">
                    <label for="i_invoice_postal_code" class="form-label">{{ __('Postal code') }}</label>
                    <input type="text" name="invoice_postal_code" id="i_invoice_postal_code" class="form-control" value="{{ old('invoice_postal_code', config('settings.invoice_postal_code')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_invoice_phone" class="form-label">{{ __('Phone') }}</label>
                    <input type="text" name="invoice_phone" id="i_invoice_phone" class="form-control" value="{{ old('invoice_phone', config('settings.invoice_phone')) }}">
                </div>

                <div class="col-md-6">
                    <label for="i_invoice_vat_number" class="form-label">{{ __('VAT number') }}</label>
                    <input type="text" name="invoice_vat_number" id="i_invoice_vat_number" class="form-control" value="{{ old('invoice_vat_number', config('settings.invoice_vat_number')) }}">
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

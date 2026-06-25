<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'invoice_vendor' => ['nullable', 'string', 'max:255'],
            'invoice_address' => ['nullable', 'string', 'max:255'],
            'invoice_city' => ['nullable', 'string', 'max:255'],
            'invoice_state' => ['nullable', 'string', 'max:255'],
            'invoice_postal_code' => ['nullable', 'string', 'max:255'],
            'invoice_country' => ['nullable', 'string', 'max:2'],
            'invoice_phone' => ['nullable', 'string', 'max:255'],
            'invoice_vat_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}

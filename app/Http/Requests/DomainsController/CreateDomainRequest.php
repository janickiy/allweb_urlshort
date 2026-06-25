<?php

namespace App\Http\Requests\DomainsController;

use App\Rules\ValidateDNSRule;
use App\Rules\ValidateDomainNameRule;
use App\Rules\DomainLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'url', 'max:255', new DomainLimitGateRule(), new ValidateDomainNameRule(), new ValidateDNSRule()],
            'index_page' => ['nullable', 'url', 'max:255'],
            'not_found_page' => ['nullable', 'url', 'max:255']
        ];
    }
}

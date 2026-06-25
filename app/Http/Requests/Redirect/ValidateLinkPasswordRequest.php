<?php

namespace App\Http\Requests\Redirect;

use App\Models\Link;
use App\Rules\ValidateLinkPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateLinkPasswordRequest extends FormRequest
{
    private ?Link $link = null;

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
            'password' => ['required', new ValidateLinkPasswordRule($this, $this->link()->password)],
        ];
    }

    public function link(): Link
    {
        return $this->link ??= Link::findOrFail($this->route('id'));
    }
}

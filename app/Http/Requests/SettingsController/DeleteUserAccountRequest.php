<?php

namespace App\Http\Requests\SettingsController;

use App\Rules\ValidateUserPasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class DeleteUserAccountRequest extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        return [
            'current_password' => ['required', 'string', 'min:6', new ValidateUserPasswordRule($request)]
        ];
    }
}

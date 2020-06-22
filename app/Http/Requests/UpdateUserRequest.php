<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        if ($request->route('id') && $request->user()->role == 0) {
            return false;
        }

        if ($request->route('id')) {
            $this->userId = $request->route('id');
        } else {
            $this->userId = $request->user()->id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->userId],
            'role'  => ['sometimes', 'integer', 'between:0,1'],
            'timezone' => ['required']
        ];
    }
}

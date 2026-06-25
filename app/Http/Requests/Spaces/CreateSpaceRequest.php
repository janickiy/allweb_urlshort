<?php

namespace App\Http\Requests\Spaces;

use App\Models\Space;
use App\Rules\SpaceLimitGateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateSpaceRequest extends FormRequest
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
            'name' => ['required', 'max:32', 'unique:' . Space::getTableName() . ',name,null,id,user_id,' . $request->user()->id, new SpaceLimitGateRule()],
            'color' => ['required']
        ];
    }
}

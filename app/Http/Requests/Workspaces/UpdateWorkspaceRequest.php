<?php

namespace App\Http\Requests\Workspaces;

use App\Models\Workspace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateWorkspaceRequest extends FormRequest
{
    /**
     * @var
     */
    private $userId;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->has('user_id') && $request->user()->role == 0) {
            return false;
        }

        if ($request->has('user_id')) {
            $this->userId = $request->input('user_id');
            Workspace::where([['id', '=', $request->route('id')], ['user_id', '=', $request->input('user_id')]])->firstOrFail();
        } else {
            $this->userId = $request->user()->id;
        }

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
            'name' => ['required', 'unique:' . Workspace::getTableName() . ',name,' . $request->route('id') . ',id,user_id,' . $this->userId],
            'color' => ['required']
        ];
    }
}

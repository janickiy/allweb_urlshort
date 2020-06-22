<?php

namespace App\Http\Requests;

use App\Link;
use App\Rules\LinkDisabledGateRule;
use App\Rules\LinkExpirationGateRule;
use App\Rules\LinkGeoGateRule;
use App\Rules\LinkPasswordGateRule;
use App\Rules\LinkPlatformGateRule;
use App\Rules\LinkPublicGateRule;
use App\Rules\LinkSpaceGateRule;
use App\Rules\ValidateAliasRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateGeoKeyRule;
use App\Rules\ValidatePlatformKeyRule;
use App\Rules\ValidateSpaceOwnershipRule;
use App\Traits\UserFeaturesTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateLinkRequest extends FormRequest
{
    use UserFeaturesTrait;

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
    public function authorize(Request $request)
    {
        if ($request->has('user_id') && $request->user()->role == 0) {
            return false;
        }

        if ($request->has('user_id')) {
            $this->userId = $request->input('user_id');
            Link::where([['id', '=', $request->route('id')], ['user_id', '=', $request->input('user_id')]])->firstOrFail();
        } else {
            $this->userId = $request->user()->id;
        }

        $this->userId = $request->user()->id;

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        $userFeatures = $this->getFeatures($request->user());

        return [
            'url' => ['sometimes', 'required', 'url', 'max:2048', new ValidateBadWordsRule()],
            'alias' => ['sometimes', 'alpha_dash', 'max:255', new ValidateAliasRule($this->userId)],
            'password' => ['nullable', 'string', 'max:128', new LinkPasswordGateRule($userFeatures)],
            'space' => ['nullable', 'integer', new ValidateSpaceOwnershipRule($this->userId), new LinkSpaceGateRule($userFeatures)],
            'disabled' => ['nullable', 'boolean', new LinkDisabledGateRule($userFeatures)],
            'public' => ['nullable', 'boolean', new LinkPublicGateRule($userFeatures)],
            'expiration_url' => ['nullable', 'url', 'max:2048', new LinkExpirationGateRule($userFeatures)],
            'expiration_date' => ['nullable', 'required_with:time', 'date_format:Y-m-d', new LinkExpirationGateRule($userFeatures)],
            'expiration_time' => ['nullable', 'required_with:date', 'date_format:H:i', new LinkExpirationGateRule($userFeatures)],
            'geo.*.key' => ['nullable', 'required_with:geo.*.value', new ValidateGeoKeyRule(), new LinkGeoGateRule($userFeatures)],
            'geo.*.value' => ['nullable', 'required_with:geo.*.key', 'max:2048', 'url'],
            'platform.*.key' => ['nullable', 'required_with:platform.*.value', new ValidatePlatformKeyRule(), new LinkPlatformGateRule($userFeatures)],
            'platform.*.value' => ['nullable', 'required_with:platform.*.key', 'max:2048', 'url']
        ];
    }

    public function attributes()
    {
        return [
            'url' => __('Link'),
            'alias' => __('Alias'),
            'password' => __('Password'),
            'space' => __('Space'),
            'disabled' => __('Disabled'),
            'public' => __('Stats'),
            'expiration_url' => __('Expiration link'),
            'expiration_date' => __('Expiration date'),
            'expiration_time' => __('Expiration time'),
            'geo.*.key' => __('Country'),
            'geo.*.value' => __('Link'),
            'platform.*.key' => __('Platform'),
            'platform.*.value' => __('Link')
        ];
    }
}

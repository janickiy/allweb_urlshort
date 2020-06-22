<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ValidateLinkPasswordRule implements Rule
{
    private $request;

    private $password;

    /**
     * Create a new rule instance.
     *
     * validatePassword constructor.
     * @param Request $request
     */
    public function __construct(Request $request, $password)
    {
        $this->request = $request;
        $this->password = $password;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (Hash::check($this->request->input($attribute), $this->password)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The entered password is not correct.');
    }
}

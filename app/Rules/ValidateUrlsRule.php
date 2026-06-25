<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateUrlsRule extends AbstractStringRule
{
    /**
     * Determine if every newline-separated value is a URL under the max length.
     */
    public function passes(string $attribute, string $value): bool
    {
        $urls = preg_split('/\n|\r/', $value, -1, PREG_SPLIT_NO_EMPTY);

        if ($urls === false) {
            return false;
        }

        foreach ($urls as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL) || mb_strlen($url) > 2048) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('validation.url');
    }
}

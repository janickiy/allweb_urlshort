<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateBadWordsRule extends AbstractStringRule
{
    /**
     * Determine if the value avoids configured banned words.
     */
    public function passes(string $attribute, string $value): bool
    {
        $bannedWords = preg_split('/\n|\r/', (string) config('settings.short_bad_words'), -1, PREG_SPLIT_NO_EMPTY);

        if ($bannedWords === false) {
            return true;
        }

        foreach ($bannedWords as $word) {
            if (strpos($value, $word) !== false) {
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
        return __('The link contains a keyword that is banned.');
    }
}

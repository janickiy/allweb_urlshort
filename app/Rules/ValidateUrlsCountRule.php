<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateUrlsCountRule extends AbstractStringRule
{
    /**
     * Create a URL-count validation rule.
     */
    public function __construct(protected readonly int $count = 10)
    {
    }

    /**
     * Determine if the newline-separated URL list is within the configured limit.
     */
    public function passes(string $attribute, string $value): bool
    {
        $urls = preg_split('/\n|\r/', $value, -1, PREG_SPLIT_NO_EMPTY);

        return $urls !== false && count($urls) <= $this->count;
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('You can\'t shorten more than :count links at once.', ['count' => $this->count]);
    }
}

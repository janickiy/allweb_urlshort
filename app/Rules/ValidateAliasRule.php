<?php

namespace App\Rules;

use App\Models\Link;
use App\Rules\Base\AbstractStringRule;

class ValidateAliasRule extends AbstractStringRule
{
    /**
     * Create a new alias uniqueness rule for a user.
     */
    public function __construct(protected readonly int|string $userId)
    {
    }

    /**
     * Determine if the alias is unique within its domain scope.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes(string $attribute, string $value): bool
    {
        $conditions = [];

        $conditions[] = ['alias', '=', $value];

        // If the query is for a specific link
        if (request()->route('id')) {
            // Exclude the link when validating the alias
            $conditions[] = ['id', '!=', request()->route('id')];

            $link = Link::findOrFail(request()->route('id'));
            $conditions[] = ['domain_id', '=', $link->domain?->id];
        } else {
            // If the request has a link under a domain
            if (request()->input('domain')) {
                $conditions[] = ['domain_id', '=', request()->input('domain')];
            } // Check for links that are not under a domain
            else {
                $conditions[] = ['domain_id', '=', null];
            }
        }

        return !Link::where($conditions)->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('validation.unique');
    }
}

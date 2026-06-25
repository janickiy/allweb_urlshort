<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateDNSRule extends AbstractStringRule
{
    /**
     * Determine if the DNS A record points to this server.
     */
    public function passes(string $attribute, string $value): bool
    {
        $parsed = parse_url($value);

        if (!is_array($parsed) || !isset($parsed['host'])) {
            return false;
        }

        try {
            $dns = dns_get_record($parsed['host']);
        } catch (\Exception) {
            return false;
        }

        if ($dns === false) {
            return false;
        }

        foreach ($dns as $record) {
            if (($record['type'] ?? null) === 'A' && ($record['ip'] ?? null) === request()->server('SERVER_ADDR')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('The DNS A record does not point to our server, or the DNS did not propagated yet, this can take up to 24 hours.');
    }
}

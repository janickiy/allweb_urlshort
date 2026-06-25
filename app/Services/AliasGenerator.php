<?php

namespace App\Services;

use App\Repositories\LinkRepository;

class AliasGenerator
{
    /**
     * Inject dependencies used to check generated aliases.
     */
    public function __construct(private readonly LinkRepository $links)
    {
    }

    /**
     * Generate a unique short-link alias for a domain scope.
     */
    public function generate(?int $domainId = null): string
    {
        $attempts = 0;

        do {
            $length = match (true) {
                $attempts > 20 => 7,
                $attempts > 10 => 6,
                default => 5,
            };

            $alias = $this->randomString($length);
            $attempts++;
        } while ($this->links->aliasExists($alias, $domainId));

        return $alias;
    }

    /**
     * Generate a random alphanumeric string of the requested length.
     */
    private function randomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($characters) - 1;
        $value = '';

        for ($i = 0; $i < $length; $i++) {
            $value .= $characters[random_int(0, $max)];
        }

        return $value;
    }
}

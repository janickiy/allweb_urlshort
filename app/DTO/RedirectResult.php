<?php

namespace App\DTO;

use App\Enums\RedirectDecision;
use App\Models\Link;

final readonly class RedirectResult
{
    /**
     * Create a redirect resolution result passed from the service to the controller.
     */
    public function __construct(
        public RedirectDecision $decision,
        public ?Link $link = null,
        public ?string $target = null,
    ) {
    }
}

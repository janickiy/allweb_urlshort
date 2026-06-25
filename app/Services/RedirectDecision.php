<?php

namespace App\Services;

use App\Models\Link;

final readonly class RedirectDecision
{
    public const TYPE_REDIRECT = 'redirect';
    public const TYPE_PREVIEW = 'preview';
    public const TYPE_EXPIRED = 'expired';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_DISABLED = 'disabled';
    public const TYPE_BANNED = 'banned';
    public const TYPE_NOT_FOUND = 'not_found';

    public function __construct(
        public string $type,
        public ?Link $link = null,
        public ?string $target = null,
    ) {
    }
}

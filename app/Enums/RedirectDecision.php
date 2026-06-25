<?php

namespace App\Enums;

enum RedirectDecision: string
{
    case Redirect = 'redirect';
    case Preview = 'preview';
    case Expired = 'expired';
    case Password = 'password';
    case Disabled = 'disabled';
    case Banned = 'banned';
    case NotFound = 'not_found';
}

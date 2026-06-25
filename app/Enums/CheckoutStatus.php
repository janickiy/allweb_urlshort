<?php

namespace App\Enums;

enum CheckoutStatus: string
{
    case Ready = 'ready';
    case Pricing = 'pricing';
    case Collect = 'collect';
    case Confirm = 'confirm';
    case Complete = 'complete';
    case Error = 'error';
    case PricingError = 'pricing_error';
}

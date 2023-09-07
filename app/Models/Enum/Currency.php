<?php

namespace App\Models\Enum;

enum Currency: string
{
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case USD = 'USD';
}

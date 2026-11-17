<?php

namespace App\Enums;

enum ConsentStatus: string
{
    case Pending = 'pending';
    case Granted = 'granted';
    case Declined = 'declined';
    case Revoked = 'revoked';
}

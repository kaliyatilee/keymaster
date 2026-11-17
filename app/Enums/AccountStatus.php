<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Active = 'active';
    case PendingConsent = 'pending_consent';
    case Suspended = 'suspended';
}

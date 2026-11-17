<?php

namespace App\Enums;

enum AccountType: string
{
    case Self = 'self';
    case SchoolManaged = 'school_managed';
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectPriority: string
{
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
}

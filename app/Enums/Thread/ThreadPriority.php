<?php

declare(strict_types=1);

namespace App\Enums\Thread;

enum ThreadPriority: int
{
    case NORMAL = 1;
    case HIGH = 2;
    case URGENT = 3;
}

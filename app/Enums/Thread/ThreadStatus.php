<?php

declare(strict_types=1);

namespace App\Enums\Thread;

enum ThreadStatus: int
{
    case NEW = 1;
    case OPEN = 2;
    case SNOOZED = 3;
    case CLOSED = 4;
}

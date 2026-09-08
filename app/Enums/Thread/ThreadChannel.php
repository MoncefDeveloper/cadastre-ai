<?php

declare(strict_types=1);

namespace App\Enums\Thread;

enum ThreadChannel: int
{
    case EMAIL = 1;
    case WHATSAPP = 2;
    case WEBFORM = 3;
}

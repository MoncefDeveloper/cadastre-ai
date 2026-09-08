<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case NEW_MESSAGE = 'new_message';
    case SYSTEM_ALERT = 'system_alert'; // Reserved for future use
}

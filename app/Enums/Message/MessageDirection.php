<?php

declare(strict_types=1);

namespace App\Enums\Message;

enum MessageDirection: int
{
    case INBOUND = 1;  // Client to Agent
    case OUTBOUND = 2; // Agent/AI to Client
    case SYSTEM = 3;   // Internal logs (e.g., "Thread transferred to John")
}

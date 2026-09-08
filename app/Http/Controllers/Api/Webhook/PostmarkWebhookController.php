<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Webhook;

use App\DTOs\Email\PostmarkInboundDTO;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessInboundEmailJob;
use App\Jobs\ProcessPostmarkBounceJob;
use App\Jobs\ProcessPostmarkDeliveryJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PostmarkWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $recordType = $payload['RecordType'] ?? 'Inbound';

        match ($recordType) {
            'Bounce' => ProcessPostmarkBounceJob::dispatch($payload),
            'Delivery' => ProcessPostmarkDeliveryJob::dispatch($payload),
            default => ProcessInboundEmailJob::dispatch(PostmarkInboundDTO::fromRequest($payload)),
        };

        return response()->json(['status' => 'queued']);
    }
}

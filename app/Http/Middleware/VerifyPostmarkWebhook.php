<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPostmarkWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.postmark.webhook_secret', env('POSTMARK_WEBHOOK_SECRET'));

        // We check a custom header that you will configure in the Postmark UI
        if ($request->header('X-Postmark-Secret') !== $secret) {
            abort(401, 'Unauthorized Postmark Webhook');
        }

        return $next($request);
    }
}

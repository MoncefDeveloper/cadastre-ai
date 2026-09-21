<?php

declare(strict_types=1);

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude Postmark inbound webhook from CSRF verification
        $middleware->preventRequestForgery(except: [
            'webhooks/postmark*',
        ]);

        // Register alias for selective route grouping
        $middleware->alias([
            'force.json' => ForceJsonResponse::class,
        ]);
    })
    ->booted(function (): void {
        // Public Reads: High throughput for Framer plans and FAQ accordions
        RateLimiter::for('public-read', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Lead Ingestion: Strict rate-limiting against automated spam form bursts
        RateLimiter::for('lead-ingest', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

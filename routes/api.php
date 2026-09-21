<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ContactStoreController;
use App\Http\Controllers\Api\V1\FaqIndexController;
use App\Http\Controllers\Api\V1\PlanIndexController;
use App\Http\Controllers\Api\Webhook\PostmarkWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// =========================================================================
// PUBLIC & INTEGRATION APIS (V1)
// =========================================================================
Route::prefix('v1')
    ->middleware(['force.json'])
    ->group(function (): void {

        // High-Throughput Public Reads (Rate Limit: 60 req/min by IP)
        Route::middleware(['throttle:public-read'])->group(function (): void {
            Route::get('/plans', PlanIndexController::class)->name('api.v1.plans.index');
            Route::get('/faqs', FaqIndexController::class)->name('api.v1.faqs.index');
        });

        // Advisory Lead Ingestion (Rate Limit: 5 req/min by IP)
        Route::middleware(['throttle:lead-ingest'])->group(function (): void {
            Route::post('/contacts', ContactStoreController::class)->name('api.v1.contacts.store');
        });
    });

// =========================================================================
// EXTERNAL INFRASTRUCTURE WEBHOOKS (Root Namespace Preserved)
// =========================================================================
Route::post('/webhooks/postmark/inbound', [PostmarkWebhookController::class, 'handle']);

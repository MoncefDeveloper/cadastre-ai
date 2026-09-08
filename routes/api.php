<?php

use App\Http\Controllers\Api\Webhook\PostmarkWebhookController;
use App\Http\Middleware\VerifyPostmarkWebhook;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/webhooks/postmark/inbound', [PostmarkWebhookController::class, 'handle']);
    // ->middleware(VerifyPostmarkWebhook::class);

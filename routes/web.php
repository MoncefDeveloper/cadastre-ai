<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Webhook\PostmarkWebhookController;
use App\Mail\TestConnectionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Root URL directs visitors straight into the Cadastre AI Auth Portal
Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/send-test-email', function () {
    Mail::to('moncefdeveloper@gmail.com')->send(new TestConnectionMail());

    return 'Postmark test email dispatched!';
});

Route::post('/webhooks/postmark', [PostmarkWebhookController::class, 'handle']);

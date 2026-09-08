<?php

use App\Http\Controllers\Api\Webhook\PostmarkWebhookController;
use App\Mail\TestConnectionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/send-test-email', function () {
    // Replace with an email address you have access to
    Mail::to('moncefdeveloper@gmail.com')->send(new TestConnectionMail());

    return 'Postmark test email dispatched!';
});


Route::post('/webhooks/postmark', [PostmarkWebhookController::class, 'handle']);

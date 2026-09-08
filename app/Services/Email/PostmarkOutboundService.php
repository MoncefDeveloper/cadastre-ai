<?php

declare(strict_types=1);

namespace App\Services\Email;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostmarkOutboundService
{
    private const API_URL = 'https://api.postmarkapp.com/email';

    public function __construct(
        private readonly string $serverToken,
        private readonly string $fromAddress,
        private readonly ?string $inboundAddress
    ) {}

    public static function make(): self
    {
        return new self(
            serverToken: config('services.postmark.key', ''),
            fromAddress: config('mail.from.address', 'info@moncefdev.me'),
            inboundAddress: config('services.postmark.inbound_address')
        );
    }

    public function sendEmail(
        string $to,
        string $subject,
        string $htmlBody,
        string $textBody,
        ?string $inReplyToMessageId = null,
        string $messageStream = 'outbound',
        ?string $threadHash = null // <-- ADDED THIS
    ): string {
        if (empty($this->serverToken) || empty($this->fromAddress)) {
            throw new Exception('Postmark configuration is missing.');
        }

        $headers = [];

        if ($inReplyToMessageId) {
            $formattedMessageId = str_starts_with($inReplyToMessageId, '<') ? $inReplyToMessageId : "<{$inReplyToMessageId}>";
            $headers[] = ['Name' => 'In-Reply-To', 'Value' => $formattedMessageId];
            $headers[] = ['Name' => 'References', 'Value' => $formattedMessageId];
        }

        $payload = [
            'From' => $this->fromAddress,
            'To' => $to,
            'Subject' => $subject,
            'HtmlBody' => $htmlBody,
            'TextBody' => $textBody,
            'MessageStream' => $messageStream,
        ];

        // INJECT THE THREAD HASH INTO THE REPLY-TO ADDRESS
        if (!empty($this->inboundAddress)) {
            if ($threadHash) {
                $parts = explode('@', $this->inboundAddress);
                if (count($parts) === 2) {
                    $payload['ReplyTo'] = $parts[0] . '+' . $threadHash . '@' . $parts[1];
                } else {
                    $payload['ReplyTo'] = $this->inboundAddress;
                }
            } else {
                $payload['ReplyTo'] = $this->inboundAddress;
            }
        }

        if (!empty($headers)) {
            $payload['Headers'] = $headers;
        }

        $response = Http::withoutVerifying()->withHeaders([
            'X-Postmark-Server-Token' => $this->serverToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(15)->post(self::API_URL, $payload);

        if (!$response->successful()) {
            throw new Exception('Postmark API Error: ' . $response->body());
        }

        return $response->json('MessageID');
    }
}

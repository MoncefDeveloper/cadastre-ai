<?php

declare(strict_types=1);

namespace App\DTOs\Email;

readonly class PostmarkInboundDTO
{
    public function __construct(
        public string $messageId,
        public string $fromEmail,
        public string $fromName,
        public string $subject,
        public string $textBody,
        public string $htmlBody,
        public ?string $mailboxHash,
        public array $headers,
        public array $attachments
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            messageId: $payload['MessageID'] ?? '',
            fromEmail: $payload['FromFull']['Email'] ?? $payload['From'] ?? '',
            fromName: $payload['FromFull']['Name'] ?? '',
            subject: $payload['Subject'] ?? '(No Subject)',

            textBody: !empty($payload['StrippedTextReply'])
                ? $payload['StrippedTextReply']
                : ($payload['TextBody'] ?? ''),

            htmlBody: $payload['HtmlBody'] ?? '',
            mailboxHash: $payload['MailboxHash'] !== '' ? $payload['MailboxHash'] : null,
            headers: $payload['Headers'] ?? [],
            attachments: $payload['Attachments'] ?? []
        );
    }

    public function getInReplyToHeader(): ?string
    {
        foreach ($this->headers as $header) {
            if (strtolower($header['Name']) === 'in-reply-to') {
                return trim($header['Value'], '<>'); // Remove the < > brackets
            }
        }
        return null;
    }

    /**
     * Helper to extract the true RFC 2822 Message-ID from the client (e.g. Gmail/Outlook)
     */
    public function getRfcMessageId(): string
    {
        foreach ($this->headers as $header) {
            if (strtolower($header['Name']) === 'message-id') {
                return trim($header['Value'], '<>'); // Remove the < > brackets
            }
        }
        return $this->messageId; // Fallback to Postmark UUID if missing
    }
}

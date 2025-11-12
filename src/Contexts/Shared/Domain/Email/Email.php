<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Email;

final class Email
{
    public function __construct(
        private readonly string $to,
        private readonly string $subject,
        private readonly string $htmlBody,
        private readonly ?string $textBody = null,
        private readonly ?string $replyTo = null
    ) {
    }

    public function to(): string
    {
        return $this->to;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function htmlBody(): string
    {
        return $this->htmlBody;
    }

    public function textBody(): ?string
    {
        return $this->textBody;
    }

    public function replyTo(): ?string
    {
        return $this->replyTo;
    }
}


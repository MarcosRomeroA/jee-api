<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Email;

use App\Contexts\Shared\Domain\Email\Email;
use App\Contexts\Shared\Domain\Email\EmailSender;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;

final class SymfonyEmailSender implements EmailSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromEmail,
        private readonly string $fromName
    ) {
    }

    public function send(Email $email): void
    {
        $message = (new SymfonyEmail())
            ->from(sprintf('%s <%s>', $this->fromName, $this->fromEmail))
            ->to($email->to())
            ->subject($email->subject())
            ->html($email->htmlBody());

        if ($email->textBody() !== null) {
            $message->text($email->textBody());
        }

        if ($email->replyTo() !== null) {
            $message->replyTo($email->replyTo());
        }

        $this->mailer->send($message);
    }
}


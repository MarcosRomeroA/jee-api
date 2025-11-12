<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ResendEmailConfirmation;

use App\Contexts\Shared\Domain\Email\Email;
use App\Contexts\Shared\Domain\Email\EmailSender as EmailSenderInterface;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\EmailConfirmation;
use App\Contexts\Web\User\Domain\EmailConfirmationRepository;
use App\Contexts\Web\User\Domain\Exception\EmailAlreadyConfirmedException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;

final class EmailConfirmationResender
{
    private const TEMPLATE_PATH = __DIR__ . '/../../../../../../templates/email/email_confirmation.html';

    public function __construct(
        private readonly EmailConfirmationRepository $emailConfirmationRepository,
        private readonly UserRepository $userRepository,
        private readonly EmailSenderInterface $emailSender,
        private readonly string $appUrl
    ) {
    }

    public function resend(Uuid $userId): void
    {
        $user = $this->userRepository->findById($userId);

        // Buscar confirmación anterior
        $existingConfirmation = $this->emailConfirmationRepository->findByUserId($userId);

        if ($existingConfirmation !== null && $existingConfirmation->isConfirmed()) {
            throw new EmailAlreadyConfirmedException();
        }

        // Si existe una confirmación pendiente, la eliminamos
        if ($existingConfirmation !== null) {
            $this->emailConfirmationRepository->delete($existingConfirmation);
        }

        // Crear nueva confirmación
        $token = EmailConfirmationToken::generate();
        $expiresAt = (new \DateTimeImmutable())->modify('+24 hours');

        $emailConfirmation = new EmailConfirmation(
            Uuid::random(),
            $user,
            $token,
            $expiresAt
        );

        $this->emailConfirmationRepository->save($emailConfirmation);

        // Enviar email
        $email = new Email(
            $user->email()->value(),
            'Confirma tu Email - Juga en Equipo',
            $this->buildEmailHtml($token),
            $this->buildEmailText($token)
        );

        $this->emailSender->send($email);
    }

    private function buildEmailHtml(EmailConfirmationToken $token): string
    {
        $confirmationUrl = sprintf(
            '%s/auth/confirm-email/%s',
            $this->appUrl,
            $token->value()
        );

        $template = file_get_contents(self::TEMPLATE_PATH);

        return str_replace('{{CONFIRMATION_URL}}', $confirmationUrl, $template);
    }

    private function buildEmailText(EmailConfirmationToken $token): string
    {
        $confirmationUrl = sprintf(
            '%s/auth/confirm-email/%s',
            $this->appUrl,
            $token->value()
        );

        return <<<TEXT
        Bienvenido a Juga en Equipo!
        
        Para confirmar tu email, haz clic en el siguiente enlace:
        {$confirmationUrl}
        
        Este enlace expirará en 24 horas.
        
        Si no solicitaste este registro, puedes ignorar este email.
        
        --
        Juga en Equipo
        TEXT;
    }
}


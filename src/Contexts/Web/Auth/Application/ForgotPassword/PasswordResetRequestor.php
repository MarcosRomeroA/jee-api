<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ForgotPassword;

use App\Contexts\Shared\Domain\Email\Email;
use App\Contexts\Shared\Domain\Email\EmailSender;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\PasswordResetToken;
use App\Contexts\Web\User\Domain\PasswordResetTokenRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\PasswordResetTokenValue;

final readonly class PasswordResetRequestor
{
    private const TEMPLATE_PATH = __DIR__ . '/../../../../../../templates/email/password_reset.html';
    private const TOKEN_EXPIRATION_HOURS = 1;

    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetTokenRepository $tokenRepository,
        private EmailSender $emailSender,
        private string $appUrl,
    ) {
    }

    public function __invoke(string $email): void
    {
        try {
            $user = $this->userRepository->findByEmail($email);
        } catch (\Exception) {
            // No revelar si el email existe o no por seguridad
            return;
        }

        // Eliminar tokens previos del usuario
        $this->tokenRepository->deleteByUserId($user->getId());

        // Generar nuevo token
        $tokenData = PasswordResetTokenValue::generate();
        $plainToken = $tokenData['plain'];
        $tokenValue = $tokenData['instance'];

        $expiresAt = (new \DateTimeImmutable())->modify('+' . self::TOKEN_EXPIRATION_HOURS . ' hour');

        $resetToken = PasswordResetToken::create(
            Uuid::random(),
            $user,
            $tokenValue,
            $expiresAt,
        );

        $this->tokenRepository->save($resetToken);

        // Enviar email
        $emailMessage = new Email(
            $user->getEmail()->value(),
            'Recuperar Contraseña - Juga en Equipo',
            $this->buildEmailHtml($plainToken),
            $this->buildEmailText($plainToken),
        );

        $this->emailSender->send($emailMessage);
    }

    private function buildEmailHtml(string $plainToken): string
    {
        $resetUrl = sprintf(
            'https://jugaenequipo.com/recover/%s',
            $plainToken,
        );

        $template = file_get_contents(self::TEMPLATE_PATH);

        return str_replace('{{RESET_URL}}', $resetUrl, $template);
    }

    private function buildEmailText(string $plainToken): string
    {
        $resetUrl = sprintf(
            'https://jugaenequipo.com/recover/%s',
            $plainToken,
        );

        return <<<TEXT
        Recuperar Contraseña - Juga en Equipo

        Recibimos una solicitud para restablecer la contraseña de tu cuenta.
        Si no realizaste esta solicitud, puedes ignorar este email.

        Para restablecer tu contraseña, visita el siguiente enlace:
        {$resetUrl}

        Este enlace expirará en 1 hora y solo puede usarse una vez.

        --
        Juga en Equipo
        TEXT;
    }
}

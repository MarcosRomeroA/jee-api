<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ValidateResetToken;

use App\Contexts\Web\User\Domain\Exception\InvalidPasswordResetTokenException;
use App\Contexts\Web\User\Domain\PasswordResetTokenRepository;
use App\Contexts\Web\User\Domain\ValueObject\PasswordResetTokenValue;

final readonly class ResetTokenValidator
{
    public function __construct(
        private PasswordResetTokenRepository $tokenRepository,
    ) {
    }

    public function __invoke(string $plainToken): ValidateResetTokenResponse
    {
        $tokenHash = hash('sha256', $plainToken);
        $resetToken = $this->tokenRepository->findByTokenHash($tokenHash);

        if ($resetToken === null || !$resetToken->isValid()) {
            throw new InvalidPasswordResetTokenException();
        }

        // Ocultar parcialmente el email por privacidad
        $email = $resetToken->getUser()->getEmail()->value();
        $maskedEmail = $this->maskEmail($email);

        return new ValidateResetTokenResponse(
            valid: true,
            email: $maskedEmail,
        );
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***.***';
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        $maskedLocal = strlen($localPart) > 2
            ? substr($localPart, 0, 2) . str_repeat('*', strlen($localPart) - 2)
            : str_repeat('*', strlen($localPart));

        return $maskedLocal . '@' . $domain;
    }
}

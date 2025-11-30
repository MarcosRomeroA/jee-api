<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ResetPassword;

use App\Contexts\Web\User\Domain\Exception\InvalidPasswordResetTokenException;
use App\Contexts\Web\User\Domain\Exception\PasswordConfirmationMismatchException;
use App\Contexts\Web\User\Domain\PasswordResetTokenRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;

final readonly class PasswordResetter
{
    public function __construct(
        private PasswordResetTokenRepository $tokenRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(
        string $plainToken,
        string $password,
        string $passwordConfirmation,
    ): void {
        // Validar que las contraseñas coincidan
        if ($password !== $passwordConfirmation) {
            throw new PasswordConfirmationMismatchException();
        }

        // Buscar el token
        $tokenHash = hash('sha256', $plainToken);
        $resetToken = $this->tokenRepository->findByTokenHash($tokenHash);

        if ($resetToken === null || !$resetToken->isValid()) {
            throw new InvalidPasswordResetTokenException();
        }

        // Obtener usuario y actualizar contraseña
        $user = $resetToken->getUser();
        $newPassword = new PasswordValue($password);
        $user->updatePassword($newPassword);

        // Guardar usuario y eliminar token
        $this->userRepository->save($user);
        $this->tokenRepository->delete($resetToken);
    }
}

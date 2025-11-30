<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface PasswordResetTokenRepository
{
    public function save(PasswordResetToken $token): void;

    public function findByTokenHash(string $tokenHash): ?PasswordResetToken;

    public function findValidByUserId(Uuid $userId): ?PasswordResetToken;

    public function deleteByUserId(Uuid $userId): void;

    public function deleteExpired(): void;

    public function delete(PasswordResetToken $token): void;
}

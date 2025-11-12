<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;

interface EmailConfirmationRepository
{
    public function save(EmailConfirmation $emailConfirmation): void;

    public function findById(Uuid $id): ?EmailConfirmation;

    public function findByToken(EmailConfirmationToken $token): ?EmailConfirmation;

    public function findByUserId(Uuid $userId): ?EmailConfirmation;

    public function delete(EmailConfirmation $emailConfirmation): void;
}


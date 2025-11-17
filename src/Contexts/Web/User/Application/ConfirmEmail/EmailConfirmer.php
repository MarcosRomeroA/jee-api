<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ConfirmEmail;

use App\Contexts\Web\User\Domain\EmailConfirmationRepository;
use App\Contexts\Web\User\Domain\Exception\EmailAlreadyConfirmedException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationExpiredException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationNotFoundException;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;

final class EmailConfirmer
{
    public function __construct(
        private readonly EmailConfirmationRepository $emailConfirmationRepository
    ) {
    }

    public function confirm(string $tokenValue): void
    {
        try {
            $token = new EmailConfirmationToken($tokenValue);
        } catch (\InvalidArgumentException $e) {
            throw new EmailConfirmationNotFoundException();
        }

        $emailConfirmation = $this->emailConfirmationRepository->findByToken($token);

        if ($emailConfirmation === null) {
            throw new EmailConfirmationNotFoundException();
        }

        if ($emailConfirmation->isConfirmed()) {
            throw new EmailAlreadyConfirmedException();
        }

        if ($emailConfirmation->isExpired()) {
            throw new EmailConfirmationExpiredException();
        }

        $emailConfirmation->confirm();

        $this->emailConfirmationRepository->save($emailConfirmation);
    }
}

<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ConfirmEmail;

use App\Contexts\Web\User\Domain\EmailConfirmationRepository;
use App\Contexts\Web\User\Domain\Exception\EmailAlreadyConfirmedException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationExpiredException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;

final readonly class EmailConfirmer
{
    public function __construct(
        private EmailConfirmationRepository $emailConfirmationRepository,
        private UserRepository $userRepository
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

        $user = $emailConfirmation->user();

        // Check if user is already verified
        if ($user->isVerified()) {
            throw new EmailAlreadyConfirmedException();
        }

        if ($emailConfirmation->isExpired()) {
            throw new EmailConfirmationExpiredException();
        }

        // Mark user as verified
        $user->markAsVerified();
        $this->userRepository->save($user);

        // Delete email confirmation record
        $this->emailConfirmationRepository->delete($emailConfirmation);
    }
}

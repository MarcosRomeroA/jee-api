<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\EmailConfirmation;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;

final class EmailConfirmationMother
{
    public static function create(
        ?Uuid $id = null,
        ?EmailConfirmationToken $token = null,
        ?\DateTimeImmutable $expiresAt = null,
        ?bool $confirmed = null
    ): EmailConfirmation {
        $emailConfirmation = new EmailConfirmation(
            $id ?? Uuid::random(),
            UserMother::random(),
            $token ?? EmailConfirmationToken::generate(),
            $expiresAt ?? (new \DateTimeImmutable())->modify('+24 hours')
        );

        if ($confirmed === true) {
            $emailConfirmation->confirm();
        }

        return $emailConfirmation;
    }

    public static function random(): EmailConfirmation
    {
        return self::create();
    }

    public static function expired(): EmailConfirmation
    {
        return self::create(
            null,
            null,
            (new \DateTimeImmutable())->modify('-1 hour')
        );
    }

    public static function confirmed(): EmailConfirmation
    {
        $user = UserMother::random();
        $user->markAsVerified();

        $emailConfirmation = new EmailConfirmation(
            Uuid::random(),
            $user,
            EmailConfirmationToken::generate(),
            (new \DateTimeImmutable())->modify('+24 hours')
        );

        $emailConfirmation->confirm();

        return $emailConfirmation;
    }
}

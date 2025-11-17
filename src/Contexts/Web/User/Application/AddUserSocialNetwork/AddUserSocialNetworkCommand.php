<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\AddUserSocialNetwork;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class AddUserSocialNetworkCommand implements Command
{
    public function __construct(
        private readonly string $userId,
        private readonly string $socialNetworkId,
        private readonly string $username
    ) {
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function socialNetworkId(): string
    {
        return $this->socialNetworkId;
    }

    public function username(): string
    {
        return $this->username;
    }
}

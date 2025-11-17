<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\RemoveUserSocialNetwork;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class RemoveUserSocialNetworkCommand implements Command
{
    public function __construct(
        private readonly string $userId,
        private readonly string $socialNetworkId
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
}

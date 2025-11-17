<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\RemoveUserSocialNetwork;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class RemoveUserSocialNetworkCommandHandler implements CommandHandler
{
    public function __construct(
        private UserSocialNetworkRemover $remover
    ) {
    }

    public function __invoke(RemoveUserSocialNetworkCommand $command): void
    {
        $userId = new Uuid($command->userId());
        $socialNetworkId = new Uuid($command->socialNetworkId());

        ($this->remover)($userId, $socialNetworkId);
    }
}

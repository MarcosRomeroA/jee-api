<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\AddUserSocialNetwork;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\SocialNetworkUsername;

final readonly class AddUserSocialNetworkCommandHandler implements CommandHandler
{
    public function __construct(
        private UserSocialNetworkAdder $adder
    ) {
    }

    public function __invoke(AddUserSocialNetworkCommand $command): void
    {
        $userId = new Uuid($command->userId());
        $socialNetworkId = new Uuid($command->socialNetworkId());
        $username = new SocialNetworkUsername($command->username());

        ($this->adder)($userId, $socialNetworkId, $username);
    }
}

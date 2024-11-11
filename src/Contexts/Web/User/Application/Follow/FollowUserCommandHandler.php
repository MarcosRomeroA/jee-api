<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Follow;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FollowUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserFollower $userFollower,
    )
    {
    }

    public function __invoke(FollowUserCommand $command): void
    {
        $userId = new Uuid($command->sessionId);
        $userToFollowId = new Uuid($command->id);

        $this->userFollower->__invoke($userId, $userToFollowId);
    }
}
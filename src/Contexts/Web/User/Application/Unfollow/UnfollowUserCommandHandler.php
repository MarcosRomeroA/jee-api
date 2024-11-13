<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Unfollow;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UnfollowUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserUnfollower $userUnfollower,
    )
    {
    }

    public function __invoke(UnfollowUserCommand $command): void
    {
        $userId = new Uuid($command->sessionId);
        $userToUnfollowId = new Uuid($command->id);

        $this->userUnfollower->__invoke($userId, $userToUnfollowId);
    }
}
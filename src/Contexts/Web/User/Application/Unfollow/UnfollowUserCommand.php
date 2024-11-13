<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Unfollow;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UnfollowUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $sessionId,
    )
    {
    }
}
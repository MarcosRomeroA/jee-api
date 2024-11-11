<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Follow;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class FollowUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $sessionId,
    )
    {
    }
}
<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreatePlayerCommand implements Command
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $gameRoleId,
        public string $gameRankId,
        public string $username
    ) {
    }
}


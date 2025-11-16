<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreatePlayerCommand implements Command
{
    /**
     * @param array<string> $gameRoleIds
     */
    public function __construct(
        public string $id,
        public string $userId,
        public array $gameRoleIds,
        public ?string $gameRankId,
        public string $username
    ) {
    }
}

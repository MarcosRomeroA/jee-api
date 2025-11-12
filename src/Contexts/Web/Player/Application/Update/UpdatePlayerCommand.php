<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdatePlayerCommand implements Command
{
    public function __construct(
        public string $id,
        public string $username,
        public string $gameRoleId,
        public string $gameRankId
    ) {
    }
}


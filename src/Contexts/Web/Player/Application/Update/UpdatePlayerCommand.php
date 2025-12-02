<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdatePlayerCommand implements Command
{
    /**
     * @param array<string> $gameRoleIds
     * @param array<string, mixed>|null $accountData
     */
    public function __construct(
        public string $id,
        public array $gameRoleIds,
        public ?array $accountData,
    ) {
    }
}

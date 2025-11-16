<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateLeader;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateTeamLeaderCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $newLeaderId,
        public string $requesterId,
    ) {}
}

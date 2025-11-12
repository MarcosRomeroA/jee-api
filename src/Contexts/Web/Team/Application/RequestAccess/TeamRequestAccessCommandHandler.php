<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RequestAccess;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TeamRequestAccessCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TeamAccessRequester $requester
    ) {
    }

    public function __invoke(TeamRequestAccessCommand $command): void
    {
        $this->requester->request(
            new Uuid($command->teamId),
            new Uuid($command->playerId)
        );
    }
}


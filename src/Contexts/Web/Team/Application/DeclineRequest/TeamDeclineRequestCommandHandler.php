<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\DeclineRequest;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class TeamDeclineRequestCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamRequestDecliner $decliner,
    ) {
    }

    public function __invoke(TeamDeclineRequestCommand $command): void
    {
        $this->decliner->__invoke(
            new Uuid($command->requestId),
            new Uuid($command->declinedByUserId),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeclineRequest;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class TournamentDeclineRequestCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentRequestDecliner $decliner,
    ) {
    }

    public function __invoke(TournamentDeclineRequestCommand $command): void
    {
        $this->decliner->__invoke(
            new Uuid($command->requestId),
            new Uuid($command->declinedByUserId),
        );
    }
}

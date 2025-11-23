<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AcceptRequest;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class TournamentAcceptRequestCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentRequestAcceptor $acceptor,
    ) {
    }

    public function __invoke(TournamentAcceptRequestCommand $command): void
    {
        $this->acceptor->__invoke(
            new Uuid($command->requestId),
            new Uuid($command->acceptedByUserId),
        );
    }
}

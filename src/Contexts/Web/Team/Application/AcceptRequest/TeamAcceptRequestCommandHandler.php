<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AcceptRequest;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TeamAcceptRequestCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TeamRequestAcceptor $acceptor
    ) {
    }

    public function __invoke(TeamAcceptRequestCommand $command): void
    {
        $this->acceptor->accept(
            new Uuid($command->requestId),
            new Uuid($command->acceptedByUserId)
        );
    }
}


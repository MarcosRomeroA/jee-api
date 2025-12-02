<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class VerifyPlayerRankCommandHandler implements CommandHandler
{
    public function __construct(
        private PlayerRankVerifier $verifier,
    ) {
    }

    public function __invoke(VerifyPlayerRankCommand $command): void
    {
        $this->verifier->__invoke(new Uuid($command->playerId));
    }
}

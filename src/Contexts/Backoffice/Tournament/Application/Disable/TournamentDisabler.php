<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Disable;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentDisabler
{
    public function __construct(
        private TournamentRepository $repository,
    ) {
    }

    public function __invoke(Uuid $tournamentId, ModerationReason $reason): void
    {
        $tournament = $this->repository->findById($tournamentId);
        $tournament->disable($reason);
        $this->repository->save($tournament);
    }
}

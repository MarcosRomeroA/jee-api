<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Enable;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentEnabler
{
    public function __construct(
        private TournamentRepository $repository,
    ) {
    }

    public function __invoke(Uuid $tournamentId): void
    {
        $tournament = $this->repository->findById($tournamentId);
        $tournament->enable();
        $this->repository->save($tournament);
    }
}

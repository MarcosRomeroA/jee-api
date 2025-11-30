<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Enable;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamEnabler
{
    public function __construct(
        private TeamRepository $repository,
    ) {
    }

    public function __invoke(Uuid $teamId): void
    {
        $team = $this->repository->findById($teamId);
        $team->enable();
        $this->repository->save($team);
    }
}

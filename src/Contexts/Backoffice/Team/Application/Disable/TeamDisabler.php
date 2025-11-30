<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Disable;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamDisabler
{
    public function __construct(
        private TeamRepository $repository,
    ) {
    }

    public function __invoke(Uuid $teamId, ModerationReason $reason): void
    {
        $team = $this->repository->findById($teamId);
        $team->disable($reason);
        $this->repository->save($team);
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class TeamFinder
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    public function find(Uuid $id): Team
    {
        $team = $this->repository->findById($id);

        if ($team === null) {
            throw new TeamNotFoundException($id->value());
        }

        return $team;
    }
}


<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Delete;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class TeamDeleter
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    public function delete(Uuid $id): void
    {
        $team = $this->repository->findById($id);

        $this->repository->delete($team);
    }
}


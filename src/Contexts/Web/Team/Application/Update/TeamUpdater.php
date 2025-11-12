<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Update;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class TeamUpdater
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    public function update(
        Uuid $id,
        string $name,
        ?string $image
    ): void {
        $team = $this->repository->findById($id);

        if ($team === null) {
            throw new TeamNotFoundException($id->value());
        }

        $team->update($name, $image);

        $this->repository->save($team);
    }
}


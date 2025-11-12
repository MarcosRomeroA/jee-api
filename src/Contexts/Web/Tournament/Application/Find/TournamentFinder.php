<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class TournamentFinder
{
    public function __construct(
        private readonly TournamentRepository $repository
    ) {
    }

    public function find(Uuid $id): Tournament
    {
        $tournament = $this->repository->findById($id);

        if ($tournament === null) {
            throw new TournamentNotFoundException($id->value());
        }

        return $tournament;
    }
}


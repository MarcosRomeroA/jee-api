<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Delete;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class TournamentDeleter
{
    public function __construct(
        private readonly TournamentRepository $tournamentRepository
    ) {
    }

    public function delete(Uuid $id): void
    {
        $tournament = $this->tournamentRepository->findById($id);
        if ($tournament === null) {
            throw new TournamentNotFoundException($id->value());
        }

        $tournament->delete();
        $this->tournamentRepository->save($tournament);
    }
}
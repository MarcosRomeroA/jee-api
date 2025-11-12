<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeleteMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\MatchNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;

final class MatchDeleter
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository
    ) {
    }

    public function delete(Uuid $matchId): void
    {
        $match = $this->matchRepository->findById($matchId);

        if ($match === null) {
            throw new MatchNotFoundException($matchId->value());
        }

        $this->matchRepository->delete($match);
    }
}


<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\MatchNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentMatch;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;

final class MatchFinder
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository
    ) {
    }

    public function find(Uuid $id): TournamentMatch
    {
        $match = $this->matchRepository->findById($id);

        if ($match === null) {
            throw new MatchNotFoundException($id->value());
        }

        return $match;
    }
}


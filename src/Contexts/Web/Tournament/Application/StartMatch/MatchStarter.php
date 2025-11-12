<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\StartMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidMatchStateException;
use App\Contexts\Web\Tournament\Domain\Exception\MatchNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;

final class MatchStarter
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository
    ) {
    }

    public function start(Uuid $matchId): void
    {
        $match = $this->matchRepository->findById($matchId);

        if ($match === null) {
            throw new MatchNotFoundException($matchId->value());
        }

        try {
            $match->start();
            $this->matchRepository->save($match);
        } catch (\DomainException $e) {
            throw new InvalidMatchStateException($e->getMessage());
        }
    }
}


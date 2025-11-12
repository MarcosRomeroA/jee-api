<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateMatchResult;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidMatchStateException;
use App\Contexts\Web\Tournament\Domain\Exception\MatchNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;
use App\Contexts\Web\Tournament\Domain\ValueObject\MatchScore;

final class MatchResultUpdater
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository
    ) {
    }

    /**
     * @param array<string, int> $scores - Array con team_id => score
     * @param string|null $winnerId - UUID del equipo ganador
     */
    public function update(
        Uuid $matchId,
        array $scores,
        ?string $winnerId = null
    ): void {
        $match = $this->matchRepository->findById($matchId);

        if ($match === null) {
            throw new MatchNotFoundException($matchId->value());
        }

        if (!$match->isInProgress() && !$match->isPending()) {
            throw new InvalidMatchStateException('Match must be in progress or pending to update results');
        }

        // Si el match está pending, iniciarlo
        if ($match->isPending()) {
            $match->start();
        }

        // Actualizar scores de cada participante
        foreach ($match->participants() as $participant) {
            $teamId = $participant->team()->id()->value();

            if (isset($scores[$teamId])) {
                $participant->setScore(new MatchScore($scores[$teamId]));
            }

            // Marcar ganador si se especificó
            if ($winnerId !== null && $teamId === $winnerId) {
                $participant->markAsWinner();
            } else {
                $participant->markAsLoser();
            }
        }

        // Si se especificó un ganador, completar el match
        if ($winnerId !== null) {
            $match->complete();
        }

        $this->matchRepository->save($match);
    }
}


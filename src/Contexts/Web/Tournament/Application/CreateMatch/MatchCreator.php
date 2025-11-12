<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\CreateMatch;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\MatchParticipant;
use App\Contexts\Web\Tournament\Domain\TournamentMatch;
use App\Contexts\Web\Tournament\Domain\TournamentMatchRepository;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class MatchCreator
{
    public function __construct(
        private readonly TournamentMatchRepository $matchRepository,
        private readonly TournamentRepository $tournamentRepository,
        private readonly TeamRepository $teamRepository
    ) {
    }

    /**
     * @param array<string> $teamIds
     */
    public function create(
        Uuid $id,
        Uuid $tournamentId,
        int $round,
        array $teamIds,
        ?string $name = null,
        ?\DateTimeImmutable $scheduledAt = null
    ): void {
        // Verificar que existe el torneo
        $tournament = $this->tournamentRepository->findById($tournamentId);
        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        // Crear el match
        $match = new TournamentMatch(
            $id,
            $tournament,
            $round,
            $name,
            $scheduledAt
        );

        // Agregar participantes
        $position = 1;
        foreach ($teamIds as $teamIdString) {
            $teamId = new Uuid($teamIdString);
            $team = $this->teamRepository->findById($teamId);

            if ($team === null) {
                throw new TeamNotFoundException($teamIdString);
            }

            $participant = new MatchParticipant(
                Uuid::random(),
                $match,
                $team,
                $position
            );

            $match->addParticipant($participant);
            $position++;
        }

        $this->matchRepository->save($match);
    }
}


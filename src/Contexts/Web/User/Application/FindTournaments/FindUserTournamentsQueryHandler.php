<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class FindUserTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentTeamRepository $tournamentTeamRepository,
    ) {}

    public function __invoke(FindUserTournamentsQuery $query): array
    {
        $userId = new Uuid($query->userId);

        // Obtener todos los TournamentTeam en los que el usuario participa
        $tournamentTeams = $this->tournamentTeamRepository->findByUserId($userId);

        $tournaments = [];
        foreach ($tournamentTeams as $tournamentTeam) {
            $tournament = $tournamentTeam->tournament();
            $tournaments[] = [
                'id' => $tournament->id()->value(),
                'name' => $tournament->name(),
                'description' => $tournament->description(),
                'isOfficial' => $tournament->isOfficial(),
                'maxTeams' => $tournament->maxTeams(),
                'startAt' => $tournament->startAt()?->format('Y-m-d\TH:i:s\Z'),
                'endAt' => $tournament->endAt()?->format('Y-m-d\TH:i:s\Z'),
                'game' => [
                    'id' => $tournament->game()->id()->value(),
                    'name' => $tournament->game()->name(),
                ],
            ];
        }

        return $tournaments;
    }
}


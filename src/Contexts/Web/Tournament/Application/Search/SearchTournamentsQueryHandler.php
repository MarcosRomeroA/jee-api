<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class SearchTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentsSearcher $searcher,
        private string $cdnBaseUrl,
        private TournamentTeamRepository $tournamentTeamRepository,
    ) {
    }

    public function __invoke(SearchTournamentsQuery $query): TournamentCollectionResponse
    {
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;
        $statusId = $query->statusId ? new Uuid($query->statusId) : null;
        $responsibleId = $query->responsibleId ? new Uuid($query->responsibleId) : null;
        $currentUserId = $query->currentUserId ? new Uuid($query->currentUserId) : null;

        // Para upcoming, excluir torneos donde el usuario ya está inscrito
        $excludeUserId = $query->upcoming && $currentUserId ? $currentUserId : null;

        $tournaments = $this->searcher->search(
            $query->name,
            $gameId,
            $statusId,
            $responsibleId,
            $query->open,
            $query->limit,
            $query->offset,
            $query->upcoming,
            $excludeUserId,
        );

        $total = $this->searcher->count($query->name, $gameId, $statusId, $responsibleId, $query->open, $query->upcoming, $excludeUserId);

        // Get tournament IDs where user is registered
        $registeredTournamentIds = [];
        if ($currentUserId !== null && !empty($tournaments)) {
            $tournamentIds = array_map(fn ($t) => $t->getId(), $tournaments);
            $registeredTournamentIds = $this->tournamentTeamRepository->findUserRegisteredTournamentIds(
                $tournamentIds,
                $currentUserId
            );
        }

        $tournamentsResponse = !empty($tournaments)
            ? array_map(
                fn ($tournament) => TournamentResponse::fromTournament(
                    $tournament,
                    $this->cdnBaseUrl,
                    in_array($tournament->getId()->value(), $registeredTournamentIds)
                ),
                $tournaments
            )
            : [];

        return new TournamentCollectionResponse($tournamentsResponse, $total, $query->limit, $query->offset);
    }
}

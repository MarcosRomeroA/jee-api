<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosterPlayers;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterPlayerResponse;
use App\Contexts\Web\Team\Application\Shared\RosterPlayersCollectionResponse;
use App\Contexts\Web\Team\Domain\RosterPlayerRepository;

final class RosterPlayersSearcher
{
    public function __construct(
        private readonly RosterPlayerRepository $rosterPlayerRepository,
        private readonly string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $rosterId, Uuid $teamId): RosterPlayersCollectionResponse
    {
        $rosterPlayers = $this->rosterPlayerRepository->findByRosterId($rosterId);

        $responses = array_map(
            fn ($rosterPlayer) => RosterPlayerResponse::fromRosterPlayer($rosterPlayer, $this->cdnBaseUrl),
            $rosterPlayers
        );

        return new RosterPlayersCollectionResponse($responses, count($responses));
    }
}

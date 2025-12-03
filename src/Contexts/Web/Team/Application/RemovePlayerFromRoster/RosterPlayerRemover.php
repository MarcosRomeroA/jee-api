<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemovePlayerFromRoster;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RosterPlayerNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\RosterPlayerRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class RosterPlayerRemover
{
    public function __construct(
        private readonly RosterPlayerRepository $rosterPlayerRepository,
        private readonly TeamRepository $teamRepository,
    ) {
    }

    public function __invoke(
        Uuid $rosterId,
        Uuid $teamId,
        Uuid $playerId,
        Uuid $requesterId,
    ): void {
        $team = $this->teamRepository->findById($teamId);

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can remove players from a roster');
        }

        $rosterPlayer = $this->rosterPlayerRepository->findByRosterAndPlayer($rosterId, $playerId);

        if ($rosterPlayer === null) {
            throw new RosterPlayerNotFoundException($playerId->value());
        }

        $this->rosterPlayerRepository->delete($rosterPlayer);
    }
}

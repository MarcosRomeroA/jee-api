<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddPlayerToRoster;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Team\Domain\Exception\MaxStartersExceededException;
use App\Contexts\Web\Team\Domain\Exception\PlayerNotInTeamException;
use App\Contexts\Web\Team\Domain\Exception\RosterLeaderAlreadyExistsException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\RosterPlayer;
use App\Contexts\Web\Team\Domain\RosterPlayerRepository;
use App\Contexts\Web\Team\Domain\RosterRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class RosterPlayerAdder
{
    private const MAX_STARTERS = 5;

    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly RosterPlayerRepository $rosterPlayerRepository,
        private readonly TeamRepository $teamRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly GameRoleRepository $gameRoleRepository,
    ) {
    }

    public function createOrUpdate(
        Uuid $id,
        Uuid $rosterId,
        Uuid $teamId,
        Uuid $playerId,
        bool $isStarter,
        bool $isLeader,
        ?Uuid $gameRoleId,
        Uuid $requesterId,
    ): void {
        $team = $this->teamRepository->findById($teamId);

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can add players to a roster');
        }

        $roster = $this->rosterRepository->findById($rosterId);
        $player = $this->playerRepository->findById($playerId);

        // Verify player belongs to a user who is a member of the team
        $playerUserId = $player->user()->getId();
        if (!$team->isMember($playerUserId)) {
            throw new PlayerNotInTeamException($playerId->value(), $teamId->value());
        }

        // Verify player is for the same game as the roster
        if (!$player->game()->getId()->equals($roster->getGame()->getId())) {
            throw new \InvalidArgumentException('Player game does not match roster game');
        }

        $gameRole = null;
        if ($gameRoleId !== null) {
            $gameRole = $this->gameRoleRepository->findById($gameRoleId);
        }

        // Check if RosterPlayer already exists (for update)
        $existingRosterPlayer = $this->rosterPlayerRepository->findByRosterAndPlayer($rosterId, $playerId);

        if ($existingRosterPlayer !== null) {
            $this->update($existingRosterPlayer, $isStarter, $isLeader, $gameRole, $roster);
        } else {
            $this->create($id, $roster, $player, $isStarter, $isLeader, $gameRole);
        }
    }

    private function create(
        Uuid $id,
        $roster,
        $player,
        bool $isStarter,
        bool $isLeader,
        $gameRole,
    ): void {
        // Validate starters count
        if ($isStarter) {
            $currentStarters = $this->rosterPlayerRepository->countStartersByRosterId($roster->getId());
            if ($currentStarters >= self::MAX_STARTERS) {
                throw new MaxStartersExceededException($roster->getId()->value());
            }
        }

        // Validate leader
        if ($isLeader && $this->rosterPlayerRepository->existsLeaderInRoster($roster->getId())) {
            throw new RosterLeaderAlreadyExistsException($roster->getId()->value());
        }

        $rosterPlayer = new RosterPlayer(
            $id,
            $roster,
            $player,
            $isStarter,
            $isLeader,
            $gameRole,
        );

        $this->rosterPlayerRepository->save($rosterPlayer);
    }

    private function update(
        RosterPlayer $existingRosterPlayer,
        bool $isStarter,
        bool $isLeader,
        $gameRole,
        $roster,
    ): void {
        // Validate starters count if changing to starter
        if ($isStarter && !$existingRosterPlayer->isStarter()) {
            $currentStarters = $this->rosterPlayerRepository->countStartersByRosterId($roster->getId());
            if ($currentStarters >= self::MAX_STARTERS) {
                throw new MaxStartersExceededException($roster->getId()->value());
            }
        }

        // Validate leader if changing to leader
        if ($isLeader && !$existingRosterPlayer->isLeader()) {
            if ($this->rosterPlayerRepository->existsLeaderInRoster($roster->getId())) {
                throw new RosterLeaderAlreadyExistsException($roster->getId()->value());
            }
        }

        $existingRosterPlayer->update($isStarter, $isLeader, $gameRole);
        $this->rosterPlayerRepository->save($existingRosterPlayer);
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\DeleteRoster;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\RosterRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final class RosterDeleter
{
    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly TeamRepository $teamRepository,
    ) {
    }

    public function __invoke(
        Uuid $rosterId,
        Uuid $teamId,
        Uuid $requesterId,
    ): void {
        $team = $this->teamRepository->findById($teamId);

        if (!$team->isOwner($requesterId)) {
            throw new UnauthorizedException('Only the team creator can delete a roster');
        }

        $roster = $this->rosterRepository->findById($rosterId);

        $this->rosterRepository->delete($roster);
    }
}

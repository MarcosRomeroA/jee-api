<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\CreateRoster;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Exception\GameNotInTeamException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\Roster;
use App\Contexts\Web\Team\Domain\RosterRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\ValueObject\RosterNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\RosterDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\RosterLogoValue;

final class RosterCreator
{
    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly TeamRepository $teamRepository,
        private readonly GameRepository $gameRepository,
        private readonly ImageUploader $imageUploader,
    ) {
    }

    public function createOrUpdate(
        Uuid $id,
        Uuid $teamId,
        Uuid $gameId,
        string $name,
        ?string $description,
        ?string $logo,
        Uuid $requesterId,
    ): void {
        if ($this->rosterRepository->existsById($id)) {
            $this->update($id, $name, $description, $logo, $requesterId);
        } else {
            $this->create($id, $teamId, $gameId, $name, $description, $logo, $requesterId);
        }
    }

    private function create(
        Uuid $id,
        Uuid $teamId,
        Uuid $gameId,
        string $name,
        ?string $description,
        ?string $logo,
        Uuid $requesterId,
    ): void {
        $team = $this->teamRepository->findById($teamId);

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can create a roster');
        }

        $game = $this->gameRepository->findById($gameId);

        if (!$team->hasGame($game)) {
            throw new GameNotInTeamException($gameId->value(), $teamId->value());
        }

        $logoFilename = $this->processLogo($id->value(), $logo);

        $roster = Roster::create(
            $id,
            $team,
            $game,
            new RosterNameValue($name),
            new RosterDescriptionValue($description),
            new RosterLogoValue($logoFilename),
        );

        $this->rosterRepository->save($roster);
    }

    private function update(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $logo,
        Uuid $requesterId,
    ): void {
        $roster = $this->rosterRepository->findById($id);
        $team = $roster->getTeam();

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can update a roster');
        }

        $logoFilename = $this->processLogo($id->value(), $logo, $roster->getLogo());

        $roster->update(
            new RosterNameValue($name),
            new RosterDescriptionValue($description),
            new RosterLogoValue($logoFilename),
        );

        $this->rosterRepository->save($roster);
    }

    private function processLogo(string $rosterId, ?string $logo, ?string $currentLogo = null): ?string
    {
        if ($logo === null) {
            return $currentLogo;
        }

        if ($this->imageUploader->isBase64Image($logo)) {
            return $this->imageUploader->upload($logo, 'roster/' . $rosterId);
        }

        return $currentLogo;
    }
}

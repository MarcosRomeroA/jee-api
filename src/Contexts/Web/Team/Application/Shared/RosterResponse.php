<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\Team\Domain\Roster;

final class RosterResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $teamId,
        public readonly string $gameId,
        public readonly string $gameName,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $logo,
        public readonly int $playersCount,
        public readonly int $startersCount,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public static function fromRoster(Roster $roster, ?FileManager $fileManager = null): self
    {
        $logoUrl = null;
        if ($roster->getLogo() !== null && $fileManager !== null) {
            $logoUrl = $fileManager->generateTemporaryUrl(
                'roster/' . $roster->getId()->value(),
                $roster->getLogo()
            );
        }

        return new self(
            $roster->getId()->value(),
            $roster->getTeam()->getId()->value(),
            $roster->getGame()->getId()->value(),
            $roster->getGame()->getName(),
            $roster->getName(),
            $roster->getDescription(),
            $logoUrl,
            $roster->getPlayersCount(),
            $roster->getStartersCount(),
            $roster->getCreatedAt()->value()->format(\DateTimeInterface::ATOM),
            $roster->getUpdatedAt()?->value()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'teamId' => $this->teamId,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'playersCount' => $this->playersCount,
            'startersCount' => $this->startersCount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}

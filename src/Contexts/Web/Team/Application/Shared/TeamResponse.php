<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\Team;

final class TeamResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly array $games,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly ?string $creatorId,
        public readonly ?string $leaderId,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?string $deletedAt,
    ) {}

    public static function fromTeam(Team $team): self
    {
        $games = [];
        foreach ($team->teamGames() as $teamGame) {
            $games[] = [
                "id" => $teamGame->game()->getId()->value(),
                "name" => $teamGame->game()->getName(),
            ];
        }

        return new self(
            $team->id()->value(),
            $games,
            $team->name(),
            $team->description(),
            $team->image(),
            $team->creator()?->getId()->value(),
            $team->leader()?->getId()->value(),
            $team->getCreatedAt()->value()->format(\DateTimeInterface::ATOM),
            $team->getUpdatedAt()?->value()->format(\DateTimeInterface::ATOM),
            $team->getDeletedAt()?->value()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "games" => $this->games,
            "name" => $this->name,
            "description" => $this->description,
            "image" => $this->image,
            "creatorId" => $this->creatorId,
            "leaderId" => $this->leaderId,
            "createdAt" => $this->createdAt,
            "updatedAt" => $this->updatedAt,
            "deletedAt" => $this->deletedAt,
        ];
    }
}

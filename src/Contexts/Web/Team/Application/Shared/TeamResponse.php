<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\Team;

final class TeamResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $gameId,
        public readonly string $gameName,
        public readonly string $name,
        public readonly ?string $image,
        public readonly string $createdAt
    ) {
    }

    public static function fromTeam(Team $team): self
    {
        return new self(
            $team->id()->value(),
            $team->game()->getId()->value(),
            $team->game()->getName(),
            $team->name(),
            $team->image(),
            $team->createdAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'name' => $this->name,
            'image' => $this->image,
            'createdAt' => $this->createdAt
        ];
    }
}


<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Game\Domain\Game;

final class GameResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $minPlayersQuantity,
        public readonly int $maxPlayersQuantity,
        public readonly string $createdAt
    ) {
    }

    public static function fromGame(Game $game): self
    {
        return new self(
            $game->id()->value(),
            $game->name(),
            $game->description(),
            $game->minPlayersQuantity(),
            $game->maxPlayersQuantity(),
            $game->createdAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'minPlayersQuantity' => $this->minPlayersQuantity,
            'maxPlayersQuantity' => $this->maxPlayersQuantity,
            'createdAt' => $this->createdAt
        ];
    }
}


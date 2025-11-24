<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Game\Domain\GameRank;

final class GameRankResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $rankId,
        public readonly string $rankName,
        public readonly ?string $rankDescription,
        public readonly int $level
    ) {
    }

    public static function fromGameRank(GameRank $gameRank): self
    {
        return new self(
            $gameRank->getId()->value(),
            $gameRank->getRank()->getId()->value(),
            $gameRank->getRank()->getName(),
            $gameRank->getRank()->getDescription(),
            $gameRank->getLevel()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rankId' => $this->rankId,
            'rankName' => $this->rankName,
            'rankDescription' => $this->rankDescription,
            'level' => $this->level
        ];
    }
}

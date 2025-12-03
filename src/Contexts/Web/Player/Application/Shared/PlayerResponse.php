<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Player\Domain\Player;

final class PlayerResponse extends Response
{
    /**
     * @param array<string, mixed>|null $accountData
     * @param array<string, mixed>|null $gameRank
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $username,
        public readonly string $gameId,
        public readonly string $gameName,
        public readonly bool $verified,
        public readonly ?string $verifiedAt,
        public readonly bool $isOwnershipVerified,
        public readonly ?string $ownershipVerifiedAt,
        public readonly ?array $accountData,
        public readonly ?array $gameRank,
    ) {
    }

    public static function fromPlayer(Player $player): self
    {
        $gameRank = null;
        if ($player->gameRank() !== null) {
            $rank = $player->gameRank();
            $gameRank = [
                'id' => $rank->getId()->value(),
                'name' => $rank->getRank()->getName(),
                'level' => $rank->getLevel(),
            ];
        }

        return new self(
            $player->id()->value(),
            $player->username(),
            $player->game()->getId()->value(),
            $player->game()->getName(),
            $player->verified(),
            $player->verifiedAt()?->format('c'),
            $player->isOwnershipVerified(),
            $player->ownershipVerifiedAt()?->format('c'),
            $player->accountData()->value(),
            $gameRank,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'verified' => $this->verified,
            'verifiedAt' => $this->verifiedAt,
            'isOwnershipVerified' => $this->isOwnershipVerified,
            'ownershipVerifiedAt' => $this->ownershipVerifiedAt,
            'accountData' => $this->accountData,
            'gameRank' => $this->gameRank,
        ];
    }
}

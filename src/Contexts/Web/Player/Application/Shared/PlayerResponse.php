<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Player\Domain\Player;

final class PlayerResponse extends Response
{
    /**
     * @param array<string, mixed>|null $accountData
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $username,
        public readonly bool $verified,
        public readonly ?array $accountData,
    ) {
    }

    public static function fromPlayer(Player $player): self
    {
        return new self(
            $player->id()->value(),
            $player->username(),
            $player->verified(),
            $player->accountData()->value(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'verified' => $this->verified,
            'accountData' => $this->accountData,
        ];
    }
}

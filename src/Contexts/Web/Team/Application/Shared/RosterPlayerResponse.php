<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\RosterPlayer;

final class RosterPlayerResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $playerId,
        public readonly ?string $playerUsername,
        public readonly string $userId,
        public readonly string $userUsername,
        public readonly string $userFirstname,
        public readonly string $userLastname,
        public readonly ?string $userProfileImage,
        public readonly bool $isStarter,
        public readonly bool $isLeader,
        public readonly ?string $gameRoleId,
        public readonly ?string $gameRoleName,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }

    public static function fromRosterPlayer(RosterPlayer $rosterPlayer, string $cdnBaseUrl): self
    {
        $player = $rosterPlayer->getPlayer();
        $user = $player->user();
        $gameRole = $rosterPlayer->getGameRole();

        return new self(
            $rosterPlayer->getId()->value(),
            $player->id()->value(),
            $player->username(),
            $user->getId()->value(),
            $user->getUsername()->value(),
            $user->getFirstname()->value(),
            $user->getLastname()->value(),
            $user->getAvatarUrl(128, $cdnBaseUrl),
            $rosterPlayer->isStarter(),
            $rosterPlayer->isLeader(),
            $gameRole?->id()->value(),
            $gameRole?->role()->getName(),
            $rosterPlayer->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $rosterPlayer->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'playerId' => $this->playerId,
            'playerUsername' => $this->playerUsername,
            'userId' => $this->userId,
            'userUsername' => $this->userUsername,
            'userFirstname' => $this->userFirstname,
            'userLastname' => $this->userLastname,
            'userProfileImage' => $this->userProfileImage,
            'isStarter' => $this->isStarter,
            'isLeader' => $this->isLeader,
            'gameRoleId' => $this->gameRoleId,
            'gameRoleName' => $this->gameRoleName,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}

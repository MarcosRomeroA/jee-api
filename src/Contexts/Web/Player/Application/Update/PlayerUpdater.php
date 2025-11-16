<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Update;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Player\Domain\Exception\GameRankNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\GameRoleNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerUpdater
{
    public function __construct(
        private readonly PlayerRepository $repository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param array<string> $gameRoleIds - Array of GameRole UUIDs
     */
    public function update(
        Uuid $id,
        string $username,
        array $gameRoleIds,
        ?Uuid $gameRankId
    ): void {
        $player = $this->repository->findById($id);
        if ($player === null) {
            throw new PlayerNotFoundException($id->value());
        }

        $gameRoles = [];
        foreach ($gameRoleIds as $gameRoleId) {
            $gameRole = $this->entityManager->getReference(GameRole::class, $gameRoleId);
            if ($gameRole === null) {
                throw new GameRoleNotFoundException($gameRoleId);
            }
            $gameRoles[] = $gameRole;
        }

        $gameRank = null;
        if ($gameRankId !== null) {
            $gameRank = $this->entityManager->getReference(GameRank::class, $gameRankId->value());
            if ($gameRank === null) {
                throw new GameRankNotFoundException($gameRankId->value());
            }
        }

        $player->update(
            new UsernameValue($username),
            $gameRoles,
            $gameRank
        );

        $this->repository->save($player);
    }
}

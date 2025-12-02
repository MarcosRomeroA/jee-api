<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Update;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Player\Domain\Exception\InvalidGameAccountDataException;
use App\Contexts\Web\Player\Domain\Exception\GameRoleNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\PlayerNotFoundException;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\GameAccountDataValue;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerUpdater
{
    public function __construct(
        private readonly PlayerRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly GameAccountRequirementRepository $gameAccountRequirementRepository,
    ) {
    }

    /**
     * @param array<string> $gameRoleIds - Array of GameRole UUIDs
     */
    public function update(
        Uuid $id,
        array $gameRoleIds,
        GameAccountDataValue $accountData,
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

        // Get gameId from player's game (direct relation)
        $gameId = $player->game()->getId();

        // Validate account data against game requirements
        $this->validateAccountData($gameId, $accountData);

        $player->update(
            $gameRoles,
            $accountData
        );

        $this->repository->save($player);
    }

    private function validateAccountData(Uuid $gameId, GameAccountDataValue $accountData): void
    {
        $requirement = $this->gameAccountRequirementRepository->findByGameId($gameId);

        if ($requirement === null) {
            return;
        }

        $requirements = $requirement->getRequirements();
        $data = $accountData->value() ?? [];
        $missingFields = [];

        foreach ($requirements as $field => $required) {
            if ($required === true && (!isset($data[$field]) || $data[$field] === '')) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new InvalidGameAccountDataException($missingFields);
        }
    }
}

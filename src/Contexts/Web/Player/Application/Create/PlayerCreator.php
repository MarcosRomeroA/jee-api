<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Player\Domain\Exception\PlayerAlreadyExistsException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class PlayerCreator
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private UserRepository $userRepository,
        private GameRoleRepository $gameRoleRepository,
        private GameRankRepository $gameRankRepository,
    ) {
    }

    /**
     * @param array<string> $gameRoleIds - Array of GameRole UUIDs
     */
    public function create(
        Uuid $id,
        Uuid $userId,
        array $gameRoleIds,
        ?Uuid $gameRankId,
        UsernameValue $username,
    ): void {
        $user = $this->userRepository->findById($userId);

        // Fetch all game roles
        $gameRoles = [];
        foreach ($gameRoleIds as $gameRoleId) {
            $gameRoles[] = $this->gameRoleRepository->findById(new Uuid($gameRoleId));
        }

        // Fetch game rank if provided
        $gameRank = null;
        if ($gameRankId !== null) {
            $gameRank = $this->gameRankRepository->findById($gameRankId);
        }

        // Check if player exists without throwing exception
        try {
            $player = $this->playerRepository->findById($id);
            $player->update($username, $gameRoles, $gameRank);
        } catch (\Exception $e) {
            // Player doesn't exist, create new one
            // Use the first game role to get the gameId for validation
            $gameId = $gameRoles[0]->game()->getId();
            if (
                $this->playerRepository->existsByUserIdAndUsernameAndGameId(
                    $userId,
                    $username,
                    $gameId,
                )
            ) {
                throw new PlayerAlreadyExistsException();
            }

            $player = Player::create(
                $id,
                $user,
                $gameRoles,
                $gameRank,
                $username,
                false,
            );
        }

        $this->playerRepository->save($player);

        // TODO: Verificar rango de forma asíncrona (opcional)
        // $this->rankVerifierService->verifyRank($player, $gameId);
    }
}

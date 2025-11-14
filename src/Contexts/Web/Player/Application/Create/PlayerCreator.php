<?php declare(strict_types=1);

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
    ) {}

    public function create(
        Uuid $id,
        Uuid $userId,
        Uuid $gameRoleId,
        Uuid $gameRankId,
        UsernameValue $username,
    ): void {
        $user = $this->userRepository->findById($userId);
        $gameRole = $this->gameRoleRepository->findById($gameRoleId);
        $gameRank = $this->gameRankRepository->findById($gameRankId);

        // Check if player exists without throwing exception
        try {
            $player = $this->playerRepository->findById($id);
            $player->update($username, $gameRole, $gameRank);
        } catch (\Exception $e) {
            // Player doesn't exist, create new one
            $gameId = $gameRole->game()->getId();
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
                $gameRole,
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

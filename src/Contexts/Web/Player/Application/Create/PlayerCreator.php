<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Player\Domain\Exception\GameRankNotFoundException;
use App\Contexts\Web\Player\Domain\Exception\GameRoleNotFoundException;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Player\Domain\RankVerifier;
use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerCreator
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RankVerifier $rankVerifierService
    ) {
    }

    /**
     * @param array<Uuid> $gameRoleIds
     */
    public function create(
        Uuid $id,
        Uuid $userId,
        Uuid $gameId,
        array $gameRoleIds,
        ?Uuid $gameRankId,
        UsernameValue $username
    ): void {
        // Verificar que existe el usuario
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new UserNotFoundException($userId->value());
        }

        // Obtener GameRank si se proporciona
        $gameRank = null;
        if ($gameRankId !== null) {
            $gameRank = $this->entityManager->getReference(GameRank::class, $gameRankId->value());
            if ($gameRank === null) {
                throw new GameRankNotFoundException($gameRankId->value());
            }
        }

        // Crear player sin verificar
        $player = new Player(
            $id,
            $user,
            $username,
            $gameRank,
            false
        );

        // Agregar roles
        foreach ($gameRoleIds as $gameRoleId) {
            $gameRole = $this->entityManager->getReference(GameRole::class, $gameRoleId->value());
            if ($gameRole === null) {
                throw new GameRoleNotFoundException($gameRoleId->value());
            }
            $player->addRole($gameRole);
        }

        $this->playerRepository->save($player);

        // Verificar rango de forma asíncrona (opcional)
        // $this->rankVerifierService->verifyRank($player, $gameId);
    }
}


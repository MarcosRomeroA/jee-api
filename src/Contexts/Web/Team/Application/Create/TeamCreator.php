<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final class TeamCreator
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
        private readonly GameRepository $gameRepository
    ) {
    }

    public function create(
        Uuid $id,
        Uuid $gameId,
        Uuid $ownerId,
        string $name,
        ?string $image
    ): void {
        $game = $this->gameRepository->findById($gameId);
        $owner = $this->userRepository->findById($ownerId);

        // Buscar si el equipo ya existe (upsert)
        try {
            $team = $this->teamRepository->findById($id);
            // Si existe, actualizar
            $team->update($name, $image);
        } catch (\Exception $e) {
            // Si no existe, crear nuevo
            $team = new Team(
                $id,
                $game,
                $owner,
                $name,
                $image
            );
        }

        $this->teamRepository->save($team);
    }
}


<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\User;
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
        // Verificar que existe el juego
        $game = $this->gameRepository->findById($gameId);
        if ($game === null) {
            throw new GameNotFoundException($gameId->value());
        }

        // Verificar que existe el usuario
        $owner = $this->userRepository->findById($ownerId);
        if ($owner === null) {
            throw new UserNotFoundException($ownerId->value());
        }

        $team = new Team(
            $id,
            $game,
            $owner,
            $name,
            $image
        );

        $this->teamRepository->save($team);
    }
}


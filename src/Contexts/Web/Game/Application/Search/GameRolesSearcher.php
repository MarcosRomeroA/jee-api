<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Shared\GameRoleCollectionResponse;
use App\Contexts\Web\Game\Application\Shared\GameRoleResponse;
use App\Contexts\Web\Game\Domain\GameRoleRepository;

final readonly class GameRolesSearcher
{
    public function __construct(
        private GameRoleRepository $repository
    ) {
    }

    public function __invoke(Uuid $gameId): GameRoleCollectionResponse
    {
        $gameRoles = $this->repository->findByGame($gameId);

        $gameRolesResponse = array_map(
            static fn ($gameRole) => GameRoleResponse::fromGameRole($gameRole),
            $gameRoles
        );

        return new GameRoleCollectionResponse($gameRolesResponse);
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindAllByGame;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Shared\GameRoleCollectionResponse;
use App\Contexts\Web\Game\Application\Shared\GameRoleResponse;
use App\Contexts\Web\Game\Domain\GameRoleRepository;

final readonly class FindAllGameRolesByGameQueryHandler implements QueryHandler
{
    public function __construct(private GameRoleRepository $repository) {}

    public function __invoke(
        FindAllGameRolesByGameQuery $query,
    ): GameRoleCollectionResponse {
        $gameId = new Uuid($query->gameId);
        $gameRoles = $this->repository->findByGame($gameId);

        $gameRolesResponse = array_map(
            static fn($gameRole) => GameRoleResponse::fromGameRole($gameRole),
            $gameRoles,
        );

        return new GameRoleCollectionResponse($gameRolesResponse);
    }
}

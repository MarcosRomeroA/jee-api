<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class GameRoleCollectionResponse extends Response
{
    /**
     * @param GameRoleResponse[] $gameRoles
     */
    public function __construct(public readonly array $gameRoles) {}

    public function toArray(): array
    {
        // Devolver directamente el array de roles sin estructura de paginación
        return array_values(
            array_map(
                static fn(GameRoleResponse $gameRole) => $gameRole->toArray(),
                $this->gameRoles,
            ),
        );
    }
}

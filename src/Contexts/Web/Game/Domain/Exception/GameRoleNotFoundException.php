<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;

final class GameRoleNotFoundException extends ApiException
{
    public function __construct(string $id)
    {
        parent::__construct(
            "Game role not found with id: $id",
            "game_role_not_found",
            404
        );
    }
}


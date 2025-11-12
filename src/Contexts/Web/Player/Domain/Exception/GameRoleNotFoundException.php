<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameRoleNotFoundException extends ApiException
{
    public function __construct(string $gameRoleId)
    {
        parent::__construct(
            "Game role with id <$gameRoleId> not found",
            'game_role_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}


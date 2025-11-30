<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class GameRequirementsNotFoundException extends ApiException
{
    public function __construct(string $gameId)
    {
        parent::__construct(
            "Requirements for game with id <$gameId> not found",
            'game_requirements_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TeamNotFoundException extends ApiException
{
    public function __construct(string $teamId)
    {
        parent::__construct(
            "Team with id <$teamId> not found",
            'team_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}


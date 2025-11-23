<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TournamentRequestAlreadyExistsException extends ApiException
{
    public function __construct(string $tournamentId, string $teamId)
    {
        parent::__construct(
            "A pending request already exists for team <$teamId> in tournament <$tournamentId>",
            'tournament_request_already_exists_exception',
            Response::HTTP_CONFLICT
        );
    }
}

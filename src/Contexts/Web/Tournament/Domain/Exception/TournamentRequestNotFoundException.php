<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TournamentRequestNotFoundException extends ApiException
{
    public function __construct(string $requestId)
    {
        parent::__construct(
            "Tournament request with id <$requestId> not found",
            'tournament_request_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

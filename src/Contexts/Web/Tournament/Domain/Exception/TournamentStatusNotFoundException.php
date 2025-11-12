<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class TournamentStatusNotFoundException extends ApiException
{
    public function __construct(string $statusId)
    {
        parent::__construct(
            "Tournament status with id <$statusId> not found",
            'tournament_status_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}


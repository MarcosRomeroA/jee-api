<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RequestNotFoundException extends ApiException
{
    public function __construct(string $requestId)
    {
        parent::__construct(
            "Team request with id <$requestId> not found",
            'request_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}


<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class MaxStartersExceededException extends ApiException
{
    public function __construct(string $rosterId)
    {
        parent::__construct(
            "Roster <$rosterId> already has the maximum number of starters (5)",
            'max_starters_exceeded_exception',
            Response::HTTP_CONFLICT
        );
    }
}

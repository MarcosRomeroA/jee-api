<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EventNotFoundException extends ApiException
{
    public function __construct(string $eventId)
    {
        parent::__construct(
            "Event with id <$eventId> not found",
            'event_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

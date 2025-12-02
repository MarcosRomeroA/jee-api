<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidGameAccountDataException extends ApiException
{
    public function __construct(array $missingFields)
    {
        $fields = implode(', ', $missingFields);
        parent::__construct(
            "Missing required account data fields: $fields",
            'invalid_game_account_data_exception',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}

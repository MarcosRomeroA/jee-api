<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class UserNotFoundException extends ApiException
{
    public function __construct(string $userId)
    {
        parent::__construct(
            "User with id <$userId> not found",
            'user_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}


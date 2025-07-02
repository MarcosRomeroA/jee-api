<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class OtherUserIsMeException extends ApiException
{
    public function __construct(
        string $message = "Other User is Me",
        string $uniqueCode = "other_user_is_me_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
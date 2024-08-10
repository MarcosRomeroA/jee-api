<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PostNotFoundException extends ApiException
{
    public function __construct(
        string $message = "Post Not Found",
        string $uniqueCode = "post_not_found_exception",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
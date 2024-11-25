<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PostAlreadyExistsException extends ApiException
{
    public function __construct(
        string $message = "Post Already Exists",
        string $uniqueCode = "post_already_exists_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class UnableToReadFileException extends ApiException
{
    public function __construct(
        string $message = "Unable to read the file",
        string $uniqueCode = "unable_to_read_file_exception",
        string $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
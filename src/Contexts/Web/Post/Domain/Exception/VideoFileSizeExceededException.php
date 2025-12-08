<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class VideoFileSizeExceededException extends ApiException
{
    public function __construct(int $sizeBytes, int $maxSizeBytes)
    {
        $sizeMb = round($sizeBytes / 1024 / 1024, 2);
        $maxSizeMb = round($maxSizeBytes / 1024 / 1024, 2);

        parent::__construct(
            sprintf('Video file size %.2fMB exceeds maximum allowed %.2fMB', $sizeMb, $maxSizeMb),
            'video_file_size_exceeded_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}

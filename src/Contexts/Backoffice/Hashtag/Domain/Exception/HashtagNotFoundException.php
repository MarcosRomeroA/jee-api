<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class HashtagNotFoundException extends ApiException
{
    public function __construct(string $hashtagId)
    {
        parent::__construct(
            "Hashtag with id <$hashtagId> not found",
            'hashtag_not_found_exception',
            Response::HTTP_NOT_FOUND
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\GetPopularHashtags;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class GetPopularHashtagsResponse extends Response
{
    /**
     * @param array<string> $hashtags
     */
    public function __construct(
        private readonly array $hashtags
    ) {
    }

    /**
     * @return array<string>
     */
    public function getHashtags(): array
    {
        return $this->hashtags;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->hashtags
        ];
    }
}

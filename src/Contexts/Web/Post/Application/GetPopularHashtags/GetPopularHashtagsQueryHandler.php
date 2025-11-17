<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\GetPopularHashtags;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Domain\HashtagRepository;

final readonly class GetPopularHashtagsQueryHandler implements QueryHandler
{
    public function __construct(
        private HashtagRepository $hashtagRepository
    ) {
    }

    public function __invoke(GetPopularHashtagsQuery $query): GetPopularHashtagsResponse
    {
        $hashtags = $this->hashtagRepository->getPopularHashtags(
            $query->getDays(),
            $query->getLimit()
        );

        return new GetPopularHashtagsResponse($hashtags);
    }
}

<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Search;

use App\Contexts\Backoffice\Hashtag\Application\Shared\HashtagCollectionResponse;
use App\Contexts\Backoffice\Hashtag\Application\Shared\HashtagResponse;
use App\Contexts\Web\Post\Domain\HashtagRepository;

final readonly class HashtagSearcher
{
    public function __construct(
        private HashtagRepository $repository
    ) {
    }

    public function __invoke(array $criteria): HashtagCollectionResponse
    {
        $criteria['includeDeleted'] = true;

        $hashtags = $this->repository->searchByCriteria($criteria);
        $total = $this->repository->countByCriteria($criteria);

        $responses = [];
        foreach ($hashtags as $hashtag) {
            $responses[] = HashtagResponse::fromEntity($hashtag);
        }

        $limit = $criteria['limit'] ?? 20;
        $offset = $criteria['offset'] ?? 0;

        return new HashtagCollectionResponse($responses, $total, $limit, $offset);
    }
}

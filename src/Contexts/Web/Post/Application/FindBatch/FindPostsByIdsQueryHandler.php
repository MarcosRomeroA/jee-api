<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\FindBatch;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Exception;

final readonly class FindPostsByIdsQueryHandler implements QueryHandler
{
    public function __construct(
        private PostsBatchFinder $finder
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(FindPostsByIdsQuery $query): array
    {
        $ids = array_map(fn (string $id) => new Uuid($id), $query->ids);

        return $this->finder->__invoke($ids, $query->currentUserId);
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostResponse;

class FindPostQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly PostFinder $finder
    )
    {
    }

    public function __invoke(FindPostQuery $query): PostResponse
    {
        $id = new Uuid($query->id);

        return $this->finder->__invoke($id);
    }
}
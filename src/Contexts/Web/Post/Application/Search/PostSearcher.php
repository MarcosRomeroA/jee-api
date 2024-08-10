<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostSearcher implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
    )
    {
    }

    public function __invoke(): PostCollectionResponse
    {
        $posts = $this->repository->searchAll();

        return new PostCollectionResponse($posts);
    }
}
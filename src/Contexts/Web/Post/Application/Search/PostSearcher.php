<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class PostSearcher implements QueryHandler
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(): PostCollectionResponse
    {
        $posts = $this->repository->searchAll();


        foreach ($posts as $post) {
            $post->setResourceUrls($this->getPostResources->__invoke($post));
        }

        return new PostCollectionResponse($posts);
    }
}
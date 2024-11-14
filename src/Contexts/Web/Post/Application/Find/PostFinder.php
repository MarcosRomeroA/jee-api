<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\GetPostResources;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\PostResource;

final readonly class PostFinder
{
    public function __construct(
        private PostRepository $repository,
        private GetPostResources $getPostResources
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Uuid $id): PostResponse
    {
        $post = $this->repository->findById($id);

        $post->setResourceUrls($this->getPostResources->__invoke($post));

        return PostResponse::fromEntity($post);
    }
}
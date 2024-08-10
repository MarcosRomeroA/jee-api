<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostFinder
{
    public function __construct(
        private PostRepository $repository,
    )
    {
    }

    public function __invoke(Uuid $id): PostResponse
    {
        $post = $this->repository->findById($id);

        return PostResponse::fromEntity($post);
    }
}
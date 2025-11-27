<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Enable;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostEnabler
{
    public function __construct(
        private PostRepository $repository,
    ) {
    }

    public function __invoke(Uuid $postId): void
    {
        $post = $this->repository->findById($postId);
        $post->enable();
        $this->repository->save($post);
    }
}

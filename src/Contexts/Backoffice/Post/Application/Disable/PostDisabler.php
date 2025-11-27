<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Disable;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\ModerationReason;
use App\Contexts\Web\Post\Domain\PostRepository;

final readonly class PostDisabler
{
    public function __construct(
        private PostRepository $repository,
    ) {
    }

    public function __invoke(Uuid $postId, ModerationReason $reason): void
    {
        $post = $this->repository->findById($postId);
        $post->disable($reason);
        $this->repository->save($post);
    }
}

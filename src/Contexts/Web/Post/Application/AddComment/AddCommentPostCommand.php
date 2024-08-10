<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddComment;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

class AddCommentPostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
        public string $commentId,
        public string $comment,
    )
    {
    }
}
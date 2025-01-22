<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Dislike;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DislikePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId
    )
    {
    }
}
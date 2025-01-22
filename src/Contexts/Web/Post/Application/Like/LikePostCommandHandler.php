<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Like;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class LikePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostLiker $liker,
    )
    {
    }

    public function __invoke(LikePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        $this->liker->__invoke($postId, $userId);
    }
}
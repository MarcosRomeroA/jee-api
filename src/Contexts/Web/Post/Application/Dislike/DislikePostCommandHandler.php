<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Dislike;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DislikePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostDisliker $disliker,
    )
    {
    }

    public function __invoke(DislikePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        $this->disliker->__invoke($postId, $userId);
    }
}
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeletePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostDeleter $deleter,
    )
    {
    }

    public function __invoke(DeletePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        $this->deleter->__invoke($postId, $userId);
    }
}
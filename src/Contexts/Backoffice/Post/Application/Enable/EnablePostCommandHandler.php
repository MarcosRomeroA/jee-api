<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class EnablePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostEnabler $enabler,
    ) {
    }

    public function __invoke(EnablePostCommand $command): void
    {
        $postId = new Uuid($command->postId);

        $this->enabler->__invoke($postId);
    }
}

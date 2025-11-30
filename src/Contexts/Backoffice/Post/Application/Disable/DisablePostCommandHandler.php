<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;

final readonly class DisablePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostDisabler $disabler,
    ) {
    }

    public function __invoke(DisablePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $reason = ModerationReason::from($command->reason);

        $this->disabler->__invoke($postId, $reason);
    }
}

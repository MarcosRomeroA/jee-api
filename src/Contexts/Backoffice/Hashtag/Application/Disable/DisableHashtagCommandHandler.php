<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DisableHashtagCommandHandler implements CommandHandler
{
    public function __construct(
        private HashtagDisabler $disabler,
    ) {
    }

    public function __invoke(DisableHashtagCommand $command): void
    {
        $hashtagId = new Uuid($command->hashtagId);

        $this->disabler->__invoke($hashtagId);
    }
}

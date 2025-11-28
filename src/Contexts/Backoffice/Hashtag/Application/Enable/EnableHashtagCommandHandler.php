<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class EnableHashtagCommandHandler implements CommandHandler
{
    public function __construct(
        private HashtagEnabler $enabler,
    ) {
    }

    public function __invoke(EnableHashtagCommand $command): void
    {
        $hashtagId = new Uuid($command->hashtagId);

        $this->enabler->__invoke($hashtagId);
    }
}

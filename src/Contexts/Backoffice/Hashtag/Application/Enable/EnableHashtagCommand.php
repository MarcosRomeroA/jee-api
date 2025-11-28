<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class EnableHashtagCommand implements Command
{
    public function __construct(
        public string $hashtagId,
    ) {
    }
}

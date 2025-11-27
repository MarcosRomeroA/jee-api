<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DisablePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $reason,
    ) {
    }
}

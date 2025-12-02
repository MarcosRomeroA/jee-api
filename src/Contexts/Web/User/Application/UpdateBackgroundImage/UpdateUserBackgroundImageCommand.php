<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateUserBackgroundImageCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $image,
    ) {
    }
}

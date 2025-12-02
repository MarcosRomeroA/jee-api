<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateTeamBackgroundImageCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $requesterId,
        public string $image,
    ) {
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AcceptRequest;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class TeamAcceptRequestCommand implements Command
{
    public function __construct(
        public string $requestId,
        public string $acceptedByUserId
    ) {
    }
}


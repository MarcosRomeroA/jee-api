<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\MarkAsRead;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class MarkAsReadCommand implements Command
{
    public function __construct(
        public string $notificationId,
        public string $sessionId
    )
    {
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\MarkAsRead;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class MarkAsReadCommandHandler implements CommandHandler
{
    public function __construct(
        private NotificationMarkAsReader $notificationMarkAsReader
    )
    {
    }

    public function __invoke(MarkAsReadCommand $command): void
    {
        $notificationId = new Uuid($command->notificationId);
        $sessionId = new Uuid($command->sessionId);

        $this->notificationMarkAsReader->__invoke($notificationId, $sessionId);
    }
}

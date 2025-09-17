<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateNotificationCommand implements Command
{
    public function __construct(
        public string $id,
        public string $notificationTypeName,
        public string $userId,
        public ?string $postId = null,
    )
    {
    }
}

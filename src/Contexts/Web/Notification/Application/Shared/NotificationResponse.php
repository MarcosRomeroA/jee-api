<?php

declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Shared;

use App\Contexts\Web\Notification\Domain\Notification;

final class NotificationResponse
{
    public function __construct(
        public string $id,
        public string $type,
        public ?string $userId,
        public ?string $username,
        public ?string $profileImage,
        public ?string $postId,
        public ?string $message,
        public ?string $teamId,
        public ?string $teamName,
        public ?string $tournamentId,
        public ?string $tournamentName,
        public string $date,
        public bool $read,
    ) {
    }

    public static function fromEntity(
        Notification $notification,
        ?string $profileImage = null,
        ?string $teamName = null,
        ?string $tournamentName = null,
    ): self {
        return new self(
            $notification->getId()->value(),
            $notification->getNotificationType()->getName(),
            $notification->getUser()?->getId()?->value(),
            $notification->getUser()?->getUsername()?->value(),
            $profileImage,
            $notification->getPost()?->getId()?->value(),
            $notification->getMessage()?->getContent()->value(),
            $notification->getTeamId(),
            $teamName,
            $notification->getTournamentId(),
            $tournamentName,
            $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            $notification->getIsRead(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain\Event;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class NotificationCreatedEvent extends DomainEvent
{
    public function __construct(
        Uuid $notificationId,
        string $notificationTypeName,
        string $userIdToNotify,
        ?string $userId,
        ?string $postId,
        ?string $messageId,
        ?string $teamId = null,
        ?string $tournamentId = null
    )
    {
        $body = [
            'notificationId' => $notificationId->value(),
            'notificationTypeName' => $notificationTypeName,
            'userIdToNotify' => $userIdToNotify,
            'userId' => $userId,
            'postId' => $postId,
            'messageId' => $messageId,
            'teamId' => $teamId,
            'tournamentId' => $tournamentId,
        ];

        parent::__construct($notificationId, $body);
    }

    public static function eventName(): string
    {
        return 'notification.created';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent
    {
        return new self(
            $aggregateId,
            $body['notificationTypeName'],
            $body['userIdToNotify'],
            $body['userId'],
            $body['postId'],
            $body['messageId'],
            $body['teamId'] ?? null,
            $body['tournamentId'] ?? null
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId()->value(),
            'notificationTypeName' => $this->body['notificationTypeName'],
            'userIdToNotify' => $this->body['userIdToNotify'],
            'userId' => $this->body['userId'],
            'postId' => $this->body['postId'],
            'messageId' => $this->body['messageId'],
            'teamId' => $this->body['teamId'],
            'tournamentId' => $this->body['tournamentId'],
        ];
    }
}

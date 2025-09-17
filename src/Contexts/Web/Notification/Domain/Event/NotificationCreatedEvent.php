<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain\Event;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class NotificationCreatedEvent extends DomainEvent
{
    public function __construct(
        string $notificationId,
        string $notificationTypeName,
        string $userIdToNotify,
        ?string $userId,
        ?string $postId,
        ?string $messageId
    )
    {
        $body = [
            'notificationId' => $notificationId,
            'notificationTypeName' => $notificationTypeName,
            'userIdToNotify' => $userIdToNotify,
            'userId' => $userId,
            'postId' => $postId,
            'messageId' => $messageId,
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
            $aggregateId->value(),
            $body['notificationTypeName'],
            $body['userIdToNotify'],
            $body['userId'],
            $body['postId'] ,
            $body['messageId']
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
            'notificationTypeName' => $this->body['notificationTypeName'],
            'userIdToNotify' => $this->body['userIdToNotify'],
            'userId' => $this->body['userId'],
            'postId' => $this->body['postId'],
            'messageId' => $this->body['messageId'],
        ];
    }
}

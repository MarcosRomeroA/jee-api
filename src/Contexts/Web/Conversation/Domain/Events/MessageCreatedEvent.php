<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class MessageCreatedEvent extends DomainEvent
{
    public function __construct(
        Uuid $messageId,
        Uuid $conversationId,
        Uuid $userId
    )
    {
        $body = [
            'messageId' => $messageId->value(),
            'conversationId' => $conversationId->value(),
            'userId' => $userId->value(),
        ];

        parent::__construct($messageId, $body);
    }

    public static function eventName(): string
    {
        return 'message.created';
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
            new Uuid($body['conversationId']),
            new Uuid($body['userId'])
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
            'messageId' => $this->body['messageId'],
            'conversationId' => new Uuid($this->body['conversationId']),
            'userId' => new Uuid($this->body['userId'])
        ];
    }
}

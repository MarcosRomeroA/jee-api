<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\CQRS\Event;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\Utils;
use RuntimeException;

final class DomainEventJsonDeserializer
{
    public function __construct(private DomainEventMapping $mapping)
    {
    }

    public function deserialize(string $domainEvent): DomainEvent
    {
        $eventData  = Utils::jsonDecode($domainEvent);
        $eventName  = $eventData['data']['type'];
        $eventClass = $this->mapping->for($eventName);

        if (null === $eventClass) {
            throw new RuntimeException("The event <$eventName> doesn't exist or has no subscribers");
        }

        return $eventClass::fromPrimitives(
            $eventData['data']['attributes']['id'],
            $eventData['data']['attributes'],
            $eventData['data']['id'],
            $eventData['data']['occurred_on']
        );
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}
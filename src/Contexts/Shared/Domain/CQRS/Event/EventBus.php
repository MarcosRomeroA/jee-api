<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Event;

interface EventBus
{
    public function publish(DomainEvent|array $events): void;
}
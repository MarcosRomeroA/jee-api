<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class Notification extends AggregateRoot
{
    private Uuid $id;

    private string $message;

    private string $isNotificationRead;

    private string $createdAt;
}
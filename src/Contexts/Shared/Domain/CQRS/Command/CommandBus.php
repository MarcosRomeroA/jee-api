<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
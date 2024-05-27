<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Query;

abstract class Response
{
    abstract public function toArray() : array;
}
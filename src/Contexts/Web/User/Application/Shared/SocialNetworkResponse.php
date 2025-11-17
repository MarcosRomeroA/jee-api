<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\SocialNetwork;

final class SocialNetworkResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $url
    ) {
    }

    public static function fromEntity(SocialNetwork $socialNetwork): self
    {
        return new self(
            $socialNetwork->id()->value(),
            $socialNetwork->name()->value(),
            $socialNetwork->code()->value(),
            $socialNetwork->url()->value()
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

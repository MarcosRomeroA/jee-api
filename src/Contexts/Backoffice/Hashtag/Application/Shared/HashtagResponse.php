<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Hashtag;

final class HashtagResponse extends Response
{
    public function __construct(
        private readonly string $id,
        private readonly string $tag,
        private readonly int $count,
        private readonly string $createdAt,
        private readonly string $updatedAt,
        private readonly bool $disabled,
        private readonly ?string $disabledAt,
    ) {
    }

    public static function fromEntity(Hashtag $hashtag): self
    {
        return new self(
            id: $hashtag->getId()->value(),
            tag: $hashtag->getTag(),
            count: $hashtag->getCount(),
            createdAt: $hashtag->getCreatedAt()->value()->format('Y-m-d\TH:i:sP'),
            updatedAt: $hashtag->getUpdatedAt()->value()->format('Y-m-d\TH:i:sP'),
            disabled: $hashtag->isDeleted(),
            disabledAt: $hashtag->getDeletedAt()?->value()?->format('Y-m-d\TH:i:sP'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'count' => $this->count,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'disabled' => $this->disabled,
            'disabledAt' => $this->disabledAt,
        ];
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchStatus;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;

final class TournamentStatusResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public static function fromEntity(TournamentStatus $status): self
    {
        return new self(
            $status->id()->value(),
            $status->name(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}

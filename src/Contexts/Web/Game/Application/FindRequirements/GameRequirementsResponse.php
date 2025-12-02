<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindRequirements;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Game\Domain\GameAccountRequirement;

final class GameRequirementsResponse extends Response
{
    public function __construct(
        private string $gameId,
        private array $requirements,
    ) {
    }

    public static function fromEntity(GameAccountRequirement $requirement): self
    {
        return new self(
            $requirement->getGame()->getId()->value(),
            $requirement->getRequirements(),
        );
    }

    public function toArray(): array
    {
        return $this->requirements;
    }
}

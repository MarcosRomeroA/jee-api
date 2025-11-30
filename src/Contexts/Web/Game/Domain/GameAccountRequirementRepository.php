<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface GameAccountRequirementRepository
{
    public function save(GameAccountRequirement $requirement): void;

    public function findByGameId(Uuid $gameId): ?GameAccountRequirement;
}

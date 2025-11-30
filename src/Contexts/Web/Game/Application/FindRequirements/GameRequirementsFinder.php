<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindRequirements;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameRequirementsNotFoundException;
use App\Contexts\Web\Game\Domain\GameAccountRequirementRepository;

final readonly class GameRequirementsFinder
{
    public function __construct(
        private GameAccountRequirementRepository $repository,
    ) {
    }

    public function __invoke(Uuid $gameId): GameRequirementsResponse
    {
        $requirement = $this->repository->findByGameId($gameId);

        if ($requirement === null) {
            throw new GameRequirementsNotFoundException($gameId->value());
        }

        return GameRequirementsResponse::fromEntity($requirement);
    }
}

<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamBackgroundImageFinder
{
    public function __construct(
        private TeamRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $teamId): BackgroundImageResponse
    {
        $team = $this->repository->findById($teamId);

        $backgroundImageUrl = $team->getBackgroundImageUrl($this->cdnBaseUrl);

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}

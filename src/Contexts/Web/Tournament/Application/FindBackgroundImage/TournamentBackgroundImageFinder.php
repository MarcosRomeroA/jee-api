<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentBackgroundImageFinder
{
    public function __construct(
        private TournamentRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $tournamentId): BackgroundImageResponse
    {
        $tournament = $this->repository->findById($tournamentId);

        $backgroundImageUrl = $tournament->getBackgroundImageUrl($this->cdnBaseUrl);

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}

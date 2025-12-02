<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentBackgroundImageFinder
{
    public function __construct(
        private TournamentRepository $repository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $tournamentId): BackgroundImageResponse
    {
        $tournament = $this->repository->findById($tournamentId);

        $backgroundImageUrl = null;
        $backgroundImageFilename = $tournament->getBackgroundImage();

        if ($backgroundImageFilename !== null) {
            $backgroundImageUrl = $this->fileManager->generateTemporaryUrl(
                'tournament/' . $tournamentId->value() . '/background',
                $backgroundImageFilename
            );
        }

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}

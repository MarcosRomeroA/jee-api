<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamBackgroundImageFinder
{
    public function __construct(
        private TeamRepository $repository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $teamId): BackgroundImageResponse
    {
        $team = $this->repository->findById($teamId);

        $backgroundImageUrl = null;
        $backgroundImageFilename = $team->getBackgroundImage();

        if ($backgroundImageFilename !== null) {
            $backgroundImageUrl = $this->fileManager->generateTemporaryUrl(
                'team/' . $teamId->value() . '/background',
                $backgroundImageFilename
            );
        }

        return new BackgroundImageResponse($backgroundImageUrl);
    }
}

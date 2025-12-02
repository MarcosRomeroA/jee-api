<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\ValueObject\TeamBackgroundImageValue;

final readonly class TeamBackgroundImageUpdater
{
    public function __construct(
        private TeamRepository $repository,
        private ImageUploader $imageUploader,
    ) {
    }

    public function __invoke(Uuid $teamId, Uuid $requesterId, string $base64Image): void
    {
        $team = $this->repository->findById($teamId);

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can update the background image');
        }

        $filename = $this->imageUploader->upload($base64Image, 'team/' . $teamId->value() . '/background');

        $team->setBackgroundImage(new TeamBackgroundImageValue($filename));

        $this->repository->save($team);
    }
}

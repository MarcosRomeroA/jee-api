<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\ValueObject\TeamNameValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamDescriptionValue;
use App\Contexts\Web\Team\Domain\ValueObject\TeamImageValue;
use App\Contexts\Web\User\Domain\UserRepository;

final class TeamCreator
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
        private readonly ImageUploader $imageUploader,
    ) {
    }

    public function createOrUpdate(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        Uuid $requesterId,
    ): void {
        if ($this->teamRepository->existsById($id)) {
            $this->update($id, $name, $description, $image, $requesterId);
        } else {
            $this->create($id, $name, $description, $image, $requesterId);
        }
    }

    private function create(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        Uuid $creatorId,
    ): void {
        $creator = $this->userRepository->findById($creatorId);

        $imageFilename = $this->processImage($id->value(), $image);

        $team = Team::create(
            $id,
            new TeamNameValue($name),
            new TeamDescriptionValue($description),
            new TeamImageValue($imageFilename),
            $creator,
        );

        $this->teamRepository->save($team);
    }

    private function update(
        Uuid $id,
        string $name,
        ?string $description,
        ?string $image,
        Uuid $requesterId,
    ): void {
        $team = $this->teamRepository->findById($id);

        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException('Only the team creator or leader can update the team');
        }

        $imageFilename = $this->processImage($id->value(), $image, $team->getImage());

        $team->update(
            new TeamNameValue($name),
            new TeamDescriptionValue($description),
            new TeamImageValue($imageFilename),
        );

        $this->teamRepository->save($team);
    }

    private function processImage(string $teamId, ?string $image, ?string $currentImage = null): ?string
    {
        if ($image === null) {
            return $currentImage;
        }

        if ($this->imageUploader->isBase64Image($image)) {
            return $this->imageUploader->upload($image, 'team/' . $teamId);
        }

        // If not base64, keep the current image (don't accept URLs)
        return $currentImage;
    }
}

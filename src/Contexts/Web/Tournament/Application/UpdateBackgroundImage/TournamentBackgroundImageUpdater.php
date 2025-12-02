<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentBackgroundImageUpdater
{
    public function __construct(
        private TournamentRepository $repository,
        private ImageUploader $imageUploader,
    ) {
    }

    public function __invoke(Uuid $tournamentId, Uuid $requesterId, string $base64Image): void
    {
        $tournament = $this->repository->findById($tournamentId);

        if (!$tournament->isResponsible($requesterId)) {
            throw new UnauthorizedException('Only the tournament responsible can update the background image');
        }

        $filename = $this->imageUploader->upload($base64Image, 'tournament/' . $tournamentId->value() . '/background');

        $tournament->setBackgroundImage($filename);

        $this->repository->save($tournament);
    }
}

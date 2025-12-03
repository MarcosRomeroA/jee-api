<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentStatusNotFoundException;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final class TournamentCreator
{
    public function __construct(
        private readonly TournamentRepository $tournamentRepository,
        private readonly GameRepository $gameRepository,
        private readonly UserRepository $userRepository,
        private readonly TournamentStatusRepository $statusRepository,
        private readonly GameRankRepository $gameRankRepository,
        private readonly ImageUploader $imageUploader,
    ) {
    }

    public function create(
        Uuid $id,
        Uuid $gameId,
        string $name,
        bool $isOfficial,
        Uuid $responsibleId,
        Uuid $creatorId,
        ?string $description = null,
        ?string $rules = null,
        ?int $maxTeams = null,
        ?string $image = null,
        ?string $prize = null,
        ?string $region = null,
        ?\DateTimeImmutable $startAt = null,
        ?\DateTimeImmutable $endAt = null,
        ?Uuid $minGameRankId = null,
        ?Uuid $maxGameRankId = null,
    ): void {
        // Set default values for optional fields
        $finalMaxTeams = $maxTeams ?? 16;
        $finalStartAt = $startAt ?? new \DateTimeImmutable();
        $finalEndAt = $endAt ?? new \DateTimeImmutable("+30 days");

        // Upsert logic: try to find and update, or create new
        try {
            $tournament = $this->tournamentRepository->findById($id);

            // Process image for update
            $imageFilename = $this->processImage($id->value(), $image, $tournament->getImage());

            // Update existing tournament
            $tournament->update(
                $name,
                $description,
                $rules,
                $finalMaxTeams,
                $isOfficial,
                $imageFilename,
                $prize,
                $region,
                $finalStartAt,
                $finalEndAt
            );
        } catch (\Exception $e) {
            // Tournament doesn't exist, create new one
            $game = $this->gameRepository->findById($gameId);
            $responsible = $this->userRepository->findById($responsibleId);
            $creator = $this->userRepository->findById($creatorId);

            $status = $this->statusRepository->findById(
                new Uuid(TournamentStatus::CREATED),
            );
            if ($status === null) {
                throw new TournamentStatusNotFoundException(
                    TournamentStatus::CREATED,
                );
            }

            $minGameRank = null;
            if ($minGameRankId !== null) {
                $minGameRank = $this->gameRankRepository->findById($minGameRankId);
            }

            $maxGameRank = null;
            if ($maxGameRankId !== null) {
                $maxGameRank = $this->gameRankRepository->findById($maxGameRankId);
            }

            // Process image for create
            $imageFilename = $this->processImage($id->value(), $image);

            $tournament = new Tournament(
                $id,
                $game,
                $status,
                $responsible,
                $creator,
                $name,
                $description,
                $rules,
                $finalMaxTeams,
                $isOfficial,
                $imageFilename,
                $prize,
                $region,
                $finalStartAt,
                $finalEndAt,
                $minGameRank,
                $maxGameRank,
            );
        }

        $this->tournamentRepository->save($tournament);
    }

    private function processImage(string $tournamentId, ?string $image, ?string $currentImage = null): ?string
    {
        if ($image === null) {
            return $currentImage;
        }

        if ($this->imageUploader->isBase64Image($image)) {
            return $this->imageUploader->upload($image, 'tournament/' . $tournamentId);
        }

        // If not base64, keep the current image (don't accept URLs)
        return $currentImage;
    }
}

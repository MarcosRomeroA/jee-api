<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Tournament\Application\Shared\TournamentImageUploader;
use App\Contexts\Web\Tournament\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\GameRankNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentStatusNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UserNotFoundException;
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
        private readonly TournamentImageUploader $imageUploader,
    ) {}

    public function create(
        Uuid $id,
        Uuid $gameId,
        string $name,
        bool $isOfficial,
        Uuid $responsibleId,
        ?string $description = null,
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

        // Procesar imagen base64 si se proporciona
        $imageFilename = null;
        if ($image !== null && str_starts_with($image, 'data:image/')) {
            try {
                $imageFilename = $this->imageUploader->uploadBase64Image($id->value(), $image);
            } catch (\Exception $e) {
                // Si falla la subida de la imagen, continuamos sin ella
                $imageFilename = null;
            }
        }

        // Upsert logic: try to find and update, or create new
        try {
            $tournament = $this->tournamentRepository->findById($id);

            // Update existing tournament
            $tournament->update(
                $name,
                $description,
                $finalMaxTeams,
                $isOfficial,
                $imageFilename ?? $tournament->image(),
                $prize,
                $region,
                $finalStartAt,
                $finalEndAt
            );
        } catch (\Exception $e) {
            // Tournament doesn't exist, create new one
            $game = $this->gameRepository->findById($gameId);
            $responsible = $this->userRepository->findById($responsibleId);

            $status = $this->statusRepository->findByName(
                TournamentStatus::CREATED,
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

            $tournament = new Tournament(
                $id,
                $game,
                $status,
                $responsible,
                $name,
                $description,
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
}

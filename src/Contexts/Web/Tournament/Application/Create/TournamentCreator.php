<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
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
        private readonly GameRankRepository $gameRankRepository
    ) {
    }

    public function create(
        Uuid $id,
        Uuid $gameId,
        Uuid $responsibleId,
        string $name,
        ?string $description,
        int $maxTeams,
        bool $isOfficial,
        ?string $image,
        ?string $prize,
        ?string $region,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        ?Uuid $minGameRankId = null,
        ?Uuid $maxGameRankId = null
    ): void {
        $game = $this->gameRepository->findById($gameId);
        $responsible = $this->userRepository->findById($responsibleId);

        $status = $this->statusRepository->findByName(TournamentStatus::CREATED);
        if ($status === null) {
            throw new TournamentStatusNotFoundException(TournamentStatus::CREATED);
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
            $maxTeams,
            $isOfficial,
            $image,
            $prize,
            $region,
            $startAt,
            $endAt,
            $minGameRank,
            $maxGameRank
        );

        $this->tournamentRepository->save($tournament);
    }
}



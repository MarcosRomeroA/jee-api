<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRankRepository;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Tournament\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\GameRankNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentStatusNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\Tournament\Domain\Tournament;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
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
        // Verificar que existe el juego
        $game = $this->gameRepository->findById($gameId);
        if ($game === null) {
            throw new GameNotFoundException($gameId->value());
        }

        // Verificar que existe el usuario responsable
        $responsible = $this->userRepository->findById($responsibleId);
        if ($responsible === null) {
            throw new UserNotFoundException($responsibleId->value());
        }

        // Buscar el estado "created"
        $status = $this->statusRepository->findByName('created');
        if ($status === null) {
            throw new TournamentStatusNotFoundException('created');
        }

        // Verificar rangos si se proporcionan
        $minGameRank = null;
        if ($minGameRankId !== null) {
            $minGameRank = $this->gameRankRepository->findById($minGameRankId);
            if ($minGameRank === null) {
                throw new GameRankNotFoundException($minGameRankId->value());
            }
        }

        $maxGameRank = null;
        if ($maxGameRankId !== null) {
            $maxGameRank = $this->gameRankRepository->findById($maxGameRankId);
            if ($maxGameRank === null) {
                throw new GameRankNotFoundException($maxGameRankId->value());
            }
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



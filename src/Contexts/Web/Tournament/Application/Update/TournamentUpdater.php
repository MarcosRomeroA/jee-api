<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Update;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final class TournamentUpdater
{
    public function __construct(
        private readonly TournamentRepository $tournamentRepository
    ) {
    }

    public function update(
        Uuid $id,
        string $name,
        ?string $description,
        int $maxTeams,
        bool $isOfficial,
        ?string $image,
        ?string $prize,
        ?string $region,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt
    ): void {
        $tournament = $this->tournamentRepository->findById($id);
        if ($tournament === null) {
            throw new TournamentNotFoundException($id->value());
        }

        $tournament->update(
            $name,
            $description,
            $maxTeams,
            $isOfficial,
            $image,
            $prize,
            $region,
            $startAt,
            $endAt
        );

        $this->tournamentRepository->save($tournament);
    }
}


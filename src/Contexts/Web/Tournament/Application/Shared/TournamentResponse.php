<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Tournament\Domain\Tournament;

final class TournamentResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $gameId,
        public readonly string $gameName,
        public readonly string $tournamentStatusId,
        public readonly ?string $minGameRankId,
        public readonly ?string $maxGameRankId,
        public readonly string $responsibleId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $registeredTeams,
        public readonly int $maxTeams,
        public readonly bool $isOfficial,
        public readonly ?string $image,
        public readonly ?string $prize,
        public readonly ?string $region,
        public readonly ?string $startAt,
        public readonly ?string $endAt,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?string $deletedAt
    ) {
    }

    public static function fromTournament(Tournament $tournament): self
    {
        return new self(
            $tournament->id()->value(),
            $tournament->game()->getId()->value(),
            $tournament->game()->getName(),
            $tournament->status()->id()->value(),
            $tournament->minGameRank()?->id()->value(),
            $tournament->maxGameRank()?->id()->value(),
            $tournament->responsible()->getId()->value(),
            $tournament->name(),
            $tournament->description(),
            $tournament->registeredTeams(),
            $tournament->maxTeams(),
            $tournament->isOfficial(),
            $tournament->image(),
            $tournament->prize(),
            $tournament->region(),
            $tournament->startAt()?->format(\DateTimeInterface::ATOM),
            $tournament->endAt()?->format(\DateTimeInterface::ATOM),
            $tournament->createdAt()->format(\DateTimeInterface::ATOM),
            $tournament->updatedAt()?->format(\DateTimeInterface::ATOM),
            $tournament->deletedAt()?->format(\DateTimeInterface::ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'tournamentStatusId' => $this->tournamentStatusId,
            'minGameRankId' => $this->minGameRankId,
            'maxGameRankId' => $this->maxGameRankId,
            'responsibleId' => $this->responsibleId,
            'name' => $this->name,
            'description' => $this->description,
            'registeredTeams' => $this->registeredTeams,
            'maxTeams' => $this->maxTeams,
            'isOfficial' => $this->isOfficial,
            'image' => $this->image,
            'prize' => $this->prize,
            'region' => $this->region,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'deletedAt' => $this->deletedAt
        ];
    }
}


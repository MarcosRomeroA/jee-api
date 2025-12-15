<?php

declare(strict_types=1);

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
        public readonly string $creatorId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $rules,
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
        public readonly ?string $deletedAt,
        public readonly bool $isUserRegistered = false,
        public readonly ?string $firstPlaceTeamId = null,
        public readonly ?string $secondPlaceTeamId = null,
        public readonly ?string $thirdPlaceTeamId = null,
    ) {
    }

    public static function fromTournament(
        Tournament $tournament,
        ?string $cdnBaseUrl = null,
        bool $isUserRegistered = false,
    ): self {
        $imageUrl = null;
        if ($cdnBaseUrl !== null) {
            $imageUrl = $tournament->getImageUrl($cdnBaseUrl);
        }

        return new self(
            $tournament->getId()->value(),
            $tournament->getGame()->getId()->value(),
            $tournament->getGame()->getName(),
            $tournament->getStatus()->getId()->value(),
            $tournament->getMinGameRank()?->getId()->value(),
            $tournament->getMaxGameRank()?->getId()->value(),
            $tournament->getResponsible()->getId()->value(),
            $tournament->getCreator()->getId()->value(),
            $tournament->getName(),
            $tournament->getDescription(),
            $tournament->getRules(),
            $tournament->getRegisteredTeams(),
            $tournament->getMaxTeams(),
            $tournament->getIsOfficial(),
            $imageUrl,
            $tournament->getPrize(),
            $tournament->getRegion(),
            $tournament->getStartAt()?->format(\DateTimeInterface::ATOM),
            $tournament->getEndAt()?->format(\DateTimeInterface::ATOM),
            $tournament->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $tournament->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            $tournament->getDeletedAt()?->format(\DateTimeInterface::ATOM),
            $isUserRegistered,
            $tournament->getFirstPlaceTeam()?->getId()->value(),
            $tournament->getSecondPlaceTeam()?->getId()->value(),
            $tournament->getThirdPlaceTeam()?->getId()->value(),
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
            'creatorId' => $this->creatorId,
            'name' => $this->name,
            'description' => $this->description,
            'rules' => $this->rules,
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
            'deletedAt' => $this->deletedAt,
            'isUserRegistered' => $this->isUserRegistered,
            'firstPlaceTeamId' => $this->firstPlaceTeamId,
            'secondPlaceTeamId' => $this->secondPlaceTeamId,
            'thirdPlaceTeamId' => $this->thirdPlaceTeamId,
        ];
    }
}

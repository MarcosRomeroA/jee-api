<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Tournament\Domain\Tournament;

final class TournamentResponse extends Response
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly ?string $description,
        private readonly ?string $image,
        private readonly string $gameId,
        private readonly string $gameName,
        private readonly string $responsibleId,
        private readonly ?string $responsibleUsername,
        private readonly ?string $responsibleEmail,
        private readonly string $creatorId,
        private readonly ?string $creatorUsername,
        private readonly ?string $creatorEmail,
        private readonly string $status,
        private readonly int $registeredTeams,
        private readonly int $maxTeams,
        private readonly bool $isOfficial,
        private readonly ?string $prize,
        private readonly ?string $region,
        private readonly string $startAt,
        private readonly string $endAt,
        private readonly bool $disabled,
        private readonly ?string $moderationReason,
        private readonly ?string $disabledAt,
        private readonly string $createdAt,
    ) {
    }

    public static function fromEntity(Tournament $tournament): self
    {
        $responsible = $tournament->getResponsible();
        $creator = $tournament->getCreator();

        return new self(
            id: $tournament->getId()->value(),
            name: $tournament->getName(),
            description: $tournament->getDescription(),
            image: $tournament->getImage(),
            gameId: $tournament->getGame()->getId()->value(),
            gameName: $tournament->getGame()->getName(),
            responsibleId: $responsible->getId()->value(),
            responsibleUsername: $responsible->getUsername()->value(),
            responsibleEmail: $responsible->getEmail()->value(),
            creatorId: $creator->getId()->value(),
            creatorUsername: $creator->getUsername()->value(),
            creatorEmail: $creator->getEmail()->value(),
            status: $tournament->getStatus()->getName(),
            registeredTeams: $tournament->getRegisteredTeams(),
            maxTeams: $tournament->getMaxTeams(),
            isOfficial: $tournament->getIsOfficial(),
            prize: $tournament->getPrize(),
            region: $tournament->getRegion(),
            startAt: $tournament->getStartAt()->format('Y-m-d\TH:i:sP'),
            endAt: $tournament->getEndAt()->format('Y-m-d\TH:i:sP'),
            disabled: $tournament->isDisabled(),
            moderationReason: $tournament->getModerationReason()?->value,
            disabledAt: $tournament->getDisabledAt()?->format('Y-m-d\TH:i:sP'),
            createdAt: $tournament->getCreatedAt()->format('Y-m-d\TH:i:sP'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'responsibleId' => $this->responsibleId,
            'responsibleUsername' => $this->responsibleUsername,
            'responsibleEmail' => $this->responsibleEmail,
            'creatorId' => $this->creatorId,
            'creatorUsername' => $this->creatorUsername,
            'creatorEmail' => $this->creatorEmail,
            'status' => $this->status,
            'registeredTeams' => $this->registeredTeams,
            'maxTeams' => $this->maxTeams,
            'isOfficial' => $this->isOfficial,
            'prize' => $this->prize,
            'region' => $this->region,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
            'disabled' => $this->disabled,
            'moderationReason' => $this->moderationReason,
            'disabledAt' => $this->disabledAt,
            'createdAt' => $this->createdAt,
        ];
    }
}

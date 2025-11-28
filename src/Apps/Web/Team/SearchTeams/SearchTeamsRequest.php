<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\SearchTeams;

use App\Contexts\Web\Team\Application\Search\SearchTeamsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchTeamsRequest
{
    public function __construct(
        #[Assert\Type("string")]
        public ?string $name = null,
        #[Assert\Type("string")]
        public ?string $gameId = null,
        #[Assert\Type("string")]
        public ?string $creatorId = null,
        #[Assert\Type("string")]
        public ?string $userId = null,
        #[Assert\Type("string")]
        public ?string $tournamentId = null,
        public bool $mine = false,
        public bool $creator = false,
        public bool $leader = false,
        #[Assert\Type("int")]
        public int $limit = 20,
        #[Assert\Type("int")]
        public int $offset = 0,
        public ?string $sessionId = null,
    ) {
    }

    public static function fromHttp(Request $request, ?string $sessionId = null): self
    {
        return new self(
            $request->query->get('name'),
            $request->query->get('gameId'),
            $request->query->get('creatorId'),
            $request->query->get('userId'),
            $request->query->get('tournamentId'),
            $request->query->getBoolean('mine', false),
            $request->query->getBoolean('creator', false),
            $request->query->getBoolean('leader', false),
            (int) $request->query->get('limit', 20),
            (int) $request->query->get('offset', 0),
            $sessionId,
        );
    }

    public function toQuery(): SearchTeamsQuery
    {
        // mine=true -> equipos donde soy creator O leader
        $ownerOrLeaderId = $this->mine ? $this->sessionId : null;
        // creator=true -> equipos donde soy creator
        $myCreatorId = $this->creator ? $this->sessionId : null;
        // leader=true -> equipos donde soy leader
        $myLeaderId = $this->leader ? $this->sessionId : null;

        return new SearchTeamsQuery(
            $this->name,
            $this->gameId,
            $this->creatorId,
            $this->userId,
            $this->tournamentId,
            $ownerOrLeaderId,
            $myCreatorId,
            $myLeaderId,
            $this->limit,
            $this->offset
        );
    }
}

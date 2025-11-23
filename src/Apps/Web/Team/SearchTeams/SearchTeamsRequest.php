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
        public ?string $q = null,
        #[Assert\Type("string")]
        public ?string $gameId = null,
        #[Assert\Type("string")]
        public ?string $creatorId = null,
        #[Assert\Type("string")]
        public ?string $userId = null,
        #[Assert\Type("string")]
        public ?string $tournamentId = null,
        #[Assert\Type("int")]
        public int $limit = 20,
        #[Assert\Type("int")]
        public int $offset = 0,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->get('q'),
            $request->query->get('gameId'),
            $request->query->get('creatorId'),
            $request->query->get('userId'),
            $request->query->get('tournamentId'),
            (int) $request->query->get('limit', 20),
            (int) $request->query->get('offset', 0)
        );
    }

    public function toQuery(): SearchTeamsQuery
    {
        return new SearchTeamsQuery(
            $this->q,
            $this->gameId,
            $this->creatorId,
            $this->userId,
            $this->tournamentId,
            $this->limit,
            $this->offset
        );
    }
}

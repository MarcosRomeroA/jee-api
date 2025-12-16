<?php

declare(strict_types=1);

namespace App\Apps\Web\User\WonTournaments;

use App\Contexts\Web\Tournament\Application\SearchUserWonTournaments\SearchUserWonTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUserWonTournamentsRequest
{
    public function __construct(
        #[Assert\Type("int")]
        #[Assert\Positive]
        public int $limit = 10,
        #[Assert\Type("int")]
        #[Assert\Positive]
        public int $page = 1,
        #[Assert\Uuid]
        public ?string $userId = null,
        #[Assert\Uuid]
        public ?string $teamId = null,
        #[Assert\Uuid]
        public ?string $tournamentId = null,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            $request->query->getInt("limit", 10),
            $request->query->getInt("page", 1),
            $request->query->get("userId"),
            $request->query->get("teamId"),
            $request->query->get("tournamentId"),
        );
    }

    public function toQuery(): SearchUserWonTournamentsQuery
    {
        return new SearchUserWonTournamentsQuery(
            $this->limit,
            $this->page,
            $this->userId,
            $this->teamId,
            $this->tournamentId,
        );
    }
}

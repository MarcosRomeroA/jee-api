<?php

declare(strict_types=1);

namespace App\Apps\Web\User\WonTournaments;

use App\Contexts\Web\Tournament\Application\SearchUserWonTournaments\SearchUserWonTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUserWonTournamentsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\Type("int")]
        #[Assert\Positive]
        public int $limit = 10,
        #[Assert\Type("int")]
        #[Assert\Positive]
        public int $page = 1,
    ) {
    }

    public static function fromHttp(string $userId, Request $request): self
    {
        return new self(
            $userId,
            $request->query->getInt("limit", 10),
            $request->query->getInt("page", 1),
        );
    }

    public function toQuery(): SearchUserWonTournamentsQuery
    {
        return new SearchUserWonTournamentsQuery(
            $this->userId,
            $this->limit,
            $this->page,
        );
    }
}

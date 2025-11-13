<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchMyTeams;

use App\Contexts\Web\Team\Application\SearchMyTeams\SearchMyTeamsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMyTeamsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $userId,

        #[Assert\Type("string")]
        public ?string $q = null,

        #[Assert\Type("string")]
        public ?string $gameId = null,

        #[Assert\Type("int")]
        public int $limit = 20,

        #[Assert\Type("int")]
        public int $offset = 0,
    ) {}

    public static function fromHttp(Request $request, string $sessionId): self
    {
        return new self(
            $sessionId,
            $request->query->get('q'),
            $request->query->get('gameId'),
            (int) $request->query->get('limit', 20),
            (int) $request->query->get('offset', 0)
        );
    }

    public function toQuery(): SearchMyTeamsQuery
    {
        return new SearchMyTeamsQuery(
            $this->userId,
            $this->q,
            $this->gameId,
            $this->limit,
            $this->offset
        );
    }
}


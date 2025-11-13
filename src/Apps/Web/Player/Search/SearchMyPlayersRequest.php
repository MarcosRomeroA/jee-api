<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Search;

use App\Contexts\Web\Player\Application\Search\MinePlayersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMyPlayersRequest
{
    public function __construct(
        public string $userId,

        #[Assert\Type("string")]
        public ?string $q = null,

        #[Assert\Type("int")]
        #[Assert\PositiveOrZero]
        public ?int $page = null,

        #[Assert\Type("int")]
        #[Assert\Positive]
        public ?int $limit = null,
    ) {}

    public static function fromHttp(Request $request, string $sessionId): self
    {
        return new self(
            $sessionId,
            $request->query->get('q'),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
    }

    public function toQuery(): MinePlayersQuery
    {
        return new MinePlayersQuery(
            $this->userId,
            $this->q,
            $this->page,
            $this->limit
        );
    }
}


<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMyTournaments;

use App\Contexts\Web\Tournament\Application\SearchMyTournaments\SearchMyTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMyTournamentsRequest
{
    public function __construct(
        public string $userId,

        #[Assert\Type("string")]
        public ?string $q = null,
    ) {}

    public static function fromHttp(Request $request, string $userId): self
    {
        return new self(
            $userId,
            $request->query->get('q')
        );
    }

    public function toQuery(): SearchMyTournamentsQuery
    {
        return new SearchMyTournamentsQuery($this->userId, $this->q);
    }
}


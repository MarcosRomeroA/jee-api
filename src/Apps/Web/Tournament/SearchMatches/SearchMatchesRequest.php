<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\SearchMatches;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMatchesRequest
{
    public function __construct(
        public string $tournamentId,

        #[Assert\Type("int")]
        #[Assert\PositiveOrZero]
        public ?int $round = null,
    ) {}

    public static function fromHttp(Request $request, string $tournamentId): self
    {
        return new self(
            $tournamentId,
            $request->query->get('round') ? (int) $request->query->get('round') : null
        );
    }
}


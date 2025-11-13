<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\UpdateMatchResult;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateMatchResultRequest
{
    public function __construct(
        public string $matchId,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public array $scores, // Array de team_id => score

        #[Assert\Uuid]
        public ?string $winnerId = null,
    ) {}

    public static function fromHttp(Request $request, string $matchId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $matchId,
            $data['scores'] ?? [],
            $data['winnerId'] ?? null
        );
    }
}


<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\CreateMatch;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMatchRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $tournamentId,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public int $round,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 2)]
        #[Assert\All([
            new Assert\Uuid()
        ])]
        public array $teamIds,

        #[Assert\Type('string')]
        #[Assert\Length(max: 100)]
        public ?string $name = null,

        #[Assert\Type('string')]
        #[Assert\DateTime(format: 'Y-m-d\TH:i:s\Z')]
        public ?string $scheduledAt = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['id'] ?? '',
            $data['tournamentId'] ?? '',
            $data['round'] ?? 0,
            $data['teamIds'] ?? [],
            $data['name'] ?? null,
            $data['scheduledAt'] ?? null
        );
    }

    public function getScheduledAtAsDateTime(): ?\DateTimeImmutable
    {
        if ($this->scheduledAt === null || $this->scheduledAt === '') {
            return null;
        }

        return new \DateTimeImmutable($this->scheduledAt);
    }
}


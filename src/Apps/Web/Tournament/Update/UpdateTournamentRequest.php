<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Update;

use App\Contexts\Web\Tournament\Application\Update\UpdateTournamentCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTournamentRequest
{
    public function __construct(
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $name,

        #[Assert\Type("string")]
        public ?string $description = null,

        #[Assert\NotBlank]
        #[Assert\Type("int")]
        #[Assert\GreaterThan(0)]
        public int $maxTeams = 0,

        #[Assert\Type("bool")]
        public bool $isOfficial = false,

        #[Assert\Type("string")]
        public ?string $image = null,

        #[Assert\Type("string")]
        public ?string $prize = null,

        #[Assert\Type("string")]
        public ?string $region = null,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $startAt = '',

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $endAt = '',
    ) {}

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['maxTeams'] ?? 0,
            $data['isOfficial'] ?? false,
            $data['image'] ?? null,
            $data['prize'] ?? null,
            $data['region'] ?? null,
            $data['startAt'] ?? '',
            $data['endAt'] ?? ''
        );
    }

    public function toCommand(): UpdateTournamentCommand
    {
        return new UpdateTournamentCommand(
            $this->id,
            $this->name,
            $this->description,
            $this->maxTeams,
            $this->isOfficial,
            $this->image,
            $this->prize,
            $this->region,
            $this->startAt,
            $this->endAt
        );
    }
}


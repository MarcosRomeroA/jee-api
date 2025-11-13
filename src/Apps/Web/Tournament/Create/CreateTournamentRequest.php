<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Create;

use App\Contexts\Web\Tournament\Application\Create\CreateTournamentCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTournamentRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $gameId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $responsibleId,

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

        #[Assert\Type("string")]
        public ?string $minGameRankId = null,

        #[Assert\Type("string")]
        public ?string $maxGameRankId = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['id'] ?? '',
            $data['gameId'] ?? '',
            $data['responsibleId'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['maxTeams'] ?? 0,
            $data['isOfficial'] ?? false,
            $data['image'] ?? null,
            $data['prize'] ?? null,
            $data['region'] ?? null,
            $data['startAt'] ?? '',
            $data['endAt'] ?? '',
            $data['minGameRankId'] ?? null,
            $data['maxGameRankId'] ?? null
        );
    }

    public function toCommand(): CreateTournamentCommand
    {
        return new CreateTournamentCommand(
            $this->id,
            $this->gameId,
            $this->responsibleId,
            $this->name,
            $this->description,
            $this->maxTeams,
            $this->isOfficial,
            $this->image,
            $this->prize,
            $this->region,
            $this->startAt,
            $this->endAt,
            $this->minGameRankId,
            $this->maxGameRankId
        );
    }
}


<?php

declare(strict_types=1);

namespace App\Apps\Web\Tournament\Create;

use App\Contexts\Web\Tournament\Application\Create\CreateTournamentCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTournamentRequest
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type("string")] public string $id,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $gameId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $name,
        #[Assert\Type("bool")] public bool $isOfficial,
        #[Assert\NotBlank] #[
            Assert\Type("string"),
        ]
        public string $responsibleId,
        #[Assert\Type("string")] public ?string $description = null,
        #[Assert\Type("string")] public ?string $rules = null,
        #[Assert\Type("int")] #[
            Assert\GreaterThan(0),
        ]
        public ?int $maxTeams = null,
        #[Assert\Type("string")] public ?string $image = null,
        #[Assert\Type("string")] public ?string $prize = null,
        #[Assert\Type("string")] public ?string $region = null,
        #[Assert\Type("string")] public ?string $startAt = null,
        #[Assert\Type("string")] public ?string $endAt = null,
        #[Assert\Type("string")] public ?string $minGameRankId = null,
        #[Assert\Type("string")] public ?string $maxGameRankId = null,
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $id,
        string $sessionId,
    ): self {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data["gameId"] ?? "",
            $data["name"] ?? "",
            filter_var($data["isOfficial"] ?? false, FILTER_VALIDATE_BOOLEAN),
            $data["responsibleId"] ?? $sessionId,
            $data["description"] ?? null,
            $data["rules"] ?? null,
            isset($data["maxTeams"]) ? (int) $data["maxTeams"] : null,
            $data["image"] ?? null,
            $data["prize"] ?? null,
            $data["region"] ?? null,
            $data["startAt"] ?? null,
            $data["endAt"] ?? null,
            $data["minGameRankId"] ?? null,
            $data["maxGameRankId"] ?? null,
        );
    }

    public function toCommand(): CreateTournamentCommand
    {
        return new CreateTournamentCommand(
            $this->id,
            $this->gameId,
            $this->name,
            $this->isOfficial,
            $this->responsibleId,
            $this->description,
            $this->rules,
            $this->maxTeams,
            $this->image,
            $this->prize,
            $this->region,
            $this->startAt,
            $this->endAt,
            $this->minGameRankId,
            $this->maxGameRankId,
        );
    }
}

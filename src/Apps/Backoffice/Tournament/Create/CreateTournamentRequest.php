<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Create;

use App\Contexts\Web\Tournament\Application\Create\CreateTournamentCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTournamentRequest
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type("string")] public string $id,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $gameId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $name,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $responsibleId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $creatorId,
        #[Assert\Type("string")] public ?string $description = null,
        #[Assert\Type("string")] public ?string $rules = null,
        #[Assert\Type("int")] #[Assert\GreaterThan(0)] public ?int $maxTeams = null,
        #[Assert\Type("string")] public ?string $image = null,
        #[Assert\Type("string")] public ?string $prize = null,
        #[Assert\Type("string")] public ?string $region = null,
        #[Assert\Type("string")] public ?string $startAt = null,
        #[Assert\Type("string")] public ?string $endAt = null,
        #[Assert\Type("string")] public ?string $minGameRankId = null,
        #[Assert\Type("string")] public ?string $maxGameRankId = null,
    ) {
    }

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data["gameId"] ?? "",
            $data["name"] ?? "",
            $data["responsibleId"] ?? "",
            $data["creatorId"] ?? "",
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
            true, // Always official from backoffice
            $this->responsibleId,
            $this->creatorId,
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


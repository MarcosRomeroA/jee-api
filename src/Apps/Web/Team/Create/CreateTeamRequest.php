<?php

declare(strict_types=1);

namespace App\Apps\Web\Team\Create;

use App\Contexts\Web\Team\Application\Create\CreateTeamCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTeamRequest
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type("string")] public string $id,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $name,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $requesterId,
        #[Assert\Type("string")] public ?string $description = null,
        #[Assert\Type("string")] public ?string $image = null,
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
            $data["name"] ?? "",
            $sessionId,
            $data["description"] ?? null,
            $data["image"] ?? null,
        );
    }

    public function toCommand(): CreateTeamCommand
    {
        return new CreateTeamCommand(
            $this->id,
            $this->name,
            $this->description,
            $this->image,
            $this->requesterId,
        );
    }
}

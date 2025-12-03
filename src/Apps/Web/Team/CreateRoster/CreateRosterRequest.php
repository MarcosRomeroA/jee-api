<?php declare(strict_types=1);

namespace App\Apps\Web\Team\CreateRoster;

use App\Contexts\Web\Team\Application\CreateRoster\CreateRosterCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateRosterRequest
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type("string")] public string $id,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $teamId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $gameId,
        #[Assert\NotBlank] #[Assert\Type("string")] public string $name,
        #[Assert\Type("string")] public ?string $description = null,
        #[Assert\Type("string")] public ?string $logo = null,
        public string $requesterId = '',
    ) {
    }

    public static function fromHttp(
        Request $request,
        string $id,
        string $teamId,
        string $sessionId,
    ): self {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $teamId,
            $data["gameId"] ?? "",
            $data["name"] ?? "",
            $data["description"] ?? null,
            $data["logo"] ?? null,
            $sessionId,
        );
    }

    public function toCommand(): CreateRosterCommand
    {
        return new CreateRosterCommand(
            $this->id,
            $this->teamId,
            $this->gameId,
            $this->name,
            $this->description,
            $this->logo,
            $this->requesterId,
        );
    }
}


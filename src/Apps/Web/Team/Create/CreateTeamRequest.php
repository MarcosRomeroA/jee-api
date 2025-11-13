<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Create;

use App\Contexts\Web\Team\Application\Create\CreateTeamCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTeamRequest
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
        public string $name,

        #[Assert\Type("string")]
        public ?string $image = null,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['id'] ?? '',
            $data['gameId'] ?? '',
            $data['name'] ?? '',
            $data['image'] ?? null
        );
    }

    public function toCommand(): CreateTeamCommand
    {
        return new CreateTeamCommand(
            $this->id,
            $this->gameId,
            $this->name,
            $this->image
        );
    }
}


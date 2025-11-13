<?php declare(strict_types=1);

namespace App\Apps\Web\Team\Update;

use App\Contexts\Web\Team\Application\Update\UpdateTeamCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTeamRequest
{
    public function __construct(
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $name,

        #[Assert\Type("string")]
        public ?string $image = null,
    ) {}

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data['name'] ?? '',
            $data['image'] ?? null
        );
    }

    public function toCommand(): UpdateTeamCommand
    {
        return new UpdateTeamCommand(
            $this->id,
            $this->name,
            $this->image
        );
    }
}


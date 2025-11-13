<?php declare(strict_types=1);

namespace App\Apps\Web\Player\Create;

use App\Contexts\Web\Player\Application\Create\CreatePlayerCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePlayerRequest
{
    public function __construct(
        public string $id,
        public string $sessionId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $gameRoleId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $gameRankId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $username,
    ) {}

    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $sessionId,
            $data['gameRoleId'] ?? '',
            $data['gameRankId'] ?? '',
            $data['username'] ?? ''
        );
    }

    public function toCommand(): CreatePlayerCommand
    {
        return new CreatePlayerCommand(
            $this->id,
            $this->sessionId,
            $this->gameRoleId,
            $this->gameRankId,
            $this->username
        );
    }
}


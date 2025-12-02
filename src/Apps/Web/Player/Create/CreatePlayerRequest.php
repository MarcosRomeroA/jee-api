<?php

declare(strict_types=1);

namespace App\Apps\Web\Player\Create;

use App\Contexts\Web\Player\Application\Create\CreatePlayerCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePlayerRequest
{
    /**
     * @param array<string> $gameRoleIds
     * @param array<string, mixed>|null $accountData
     */
    public function __construct(
        public string $id,
        public string $sessionId,
        #[Assert\NotBlank]
        public string $gameId,
        #[Assert\Type("array")]
        public array $gameRoleIds,
        #[Assert\Type("array")]
        public ?array $accountData,
    ) {
    }

    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        // Support both old format (gameRoleId) and new format (gameRoleIds)
        $gameRoleIds = [];
        if (isset($data['gameRoleIds']) && is_array($data['gameRoleIds'])) {
            $gameRoleIds = $data['gameRoleIds'];
        } elseif (isset($data['gameRoleId'])) {
            // Backward compatibility: convert single gameRoleId to array
            $gameRoleIds = [$data['gameRoleId']];
        }

        return new self(
            $id,
            $sessionId,
            $data['gameId'] ?? '',
            $gameRoleIds,
            $data['accountData'] ?? null,
        );
    }

    public function toCommand(): CreatePlayerCommand
    {
        return new CreatePlayerCommand(
            $this->id,
            $this->sessionId,
            $this->gameId,
            $this->gameRoleIds,
            $this->accountData,
        );
    }
}

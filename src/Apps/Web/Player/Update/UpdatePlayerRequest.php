<?php

declare(strict_types=1);

namespace App\Apps\Web\Player\Update;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdatePlayerRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $username;

    #[Assert\Type("array")]
    public mixed $gameRoleIds;

    #[Assert\Type("string")]
    public mixed $gameRoleId;

    #[Assert\Type("string")]
    public mixed $gameRankId;

    public function getGameRoleIds(): array
    {
        // Support both old format (gameRoleId) and new format (gameRoleIds)
        if (isset($this->gameRoleIds) && is_array($this->gameRoleIds)) {
            return $this->gameRoleIds;
        } elseif (isset($this->gameRoleId)) {
            return [$this->gameRoleId];
        }
        return [];
    }

    public function getGameRankId(): ?string
    {
        return $this->gameRankId ?? null;
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Game\Domain\GameRole;

final class GameRoleResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $roleId,
        public readonly string $roleName,
        public readonly ?string $roleDescription
    ) {
    }

    public static function fromGameRole(GameRole $gameRole): self
    {
        return new self(
            $gameRole->id()->value(),
            $gameRole->role()->id()->value(),
            $gameRole->role()->name(),
            $gameRole->role()->description()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'roleId' => $this->roleId,
            'roleName' => $this->roleName,
            'roleDescription' => $this->roleDescription
        ];
    }
}

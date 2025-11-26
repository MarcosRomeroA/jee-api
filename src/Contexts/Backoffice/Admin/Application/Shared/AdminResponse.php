<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Shared;

use App\Contexts\Backoffice\Admin\Domain\Admin;
use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class AdminResponse extends Response
{
    public function __construct(
        private string $id,
        private string $name,
        private string $user,
        private string $role,
        private string $createdAt,
        private ?string $updatedAt = null,
        private ?string $deletedAt = null,
    ) {
    }

    public static function fromEntity(Admin $admin): self
    {
        return new self(
            $admin->getId()->value(),
            $admin->getName()->value(),
            $admin->getUser()->value(),
            $admin->getRole()->value,
            $admin->getCreatedAt()->format('Y-m-d H:i:s'),
            $admin->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $admin->getDeletedAt()?->format('Y-m-d H:i:s'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => $this->user,
            'role' => $this->role,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'deletedAt' => $this->deletedAt,
        ];
    }
}

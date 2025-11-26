<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain;

use App\Contexts\Backoffice\Admin\Domain\Events\AdminCreatedDomainEvent;
use App\Contexts\Backoffice\Admin\Domain\Events\AdminUpdatedDomainEvent;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminNameValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminPasswordValue;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: '`admin`')]
class Admin extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: AdminNameValue::class, columnPrefix: false)]
    private AdminNameValue $name;

    #[Embedded(class: AdminUserValue::class, columnPrefix: false)]
    private AdminUserValue $user;

    #[Embedded(class: AdminPasswordValue::class, columnPrefix: false)]
    private AdminPasswordValue $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(type: 'string', length: 20, enumType: AdminRole::class)]
    private AdminRole $role;

    private function __construct(
        Uuid $id,
        AdminNameValue $name,
        AdminUserValue $user,
        AdminPasswordValue $password,
        AdminRole $role = AdminRole::ADMIN,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Uuid $id,
        AdminNameValue $name,
        AdminUserValue $user,
        AdminPasswordValue $password,
        AdminRole $role = AdminRole::ADMIN,
    ): self {
        $admin = new self($id, $name, $user, $password, $role);

        $admin->record(new AdminCreatedDomainEvent($id));

        return $admin;
    }

    public function update(
        AdminNameValue $name,
        AdminUserValue $user,
    ): self {
        $this->name = $name;
        $this->user = $user;
        $this->updatedAt = new \DateTimeImmutable();

        $this->record(new AdminUpdatedDomainEvent($this->id));

        return $this;
    }

    public function updatePassword(AdminPasswordValue $password): self
    {
        $this->password = $password;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): AdminNameValue
    {
        return $this->name;
    }

    public function getUser(): AdminUserValue
    {
        return $this->user;
    }

    public function getPassword(): AdminPasswordValue
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function getRole(): AdminRole
    {
        return $this->role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->isSuperAdmin();
    }

    public function canManageAdmins(): bool
    {
        return $this->role->canManageAdmins();
    }
}

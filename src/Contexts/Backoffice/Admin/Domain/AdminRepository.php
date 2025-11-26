<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Domain;

use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface AdminRepository
{
    public function save(Admin $admin): void;

    public function findById(Uuid $id): ?Admin;

    public function findByUser(AdminUserValue $user): ?Admin;

    public function existsByUser(AdminUserValue $user): bool;

    public function searchByCriteria(array $criteria): array;

    public function countByCriteria(array $criteria): int;
}

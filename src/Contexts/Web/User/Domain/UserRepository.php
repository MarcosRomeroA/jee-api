<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface UserRepository
{
    public function save(User $user): void;
    public function searchAll(): array;
    public function findByEmail(string $email): User;
    public function findById(Uuid $id): User;
}
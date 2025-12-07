<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface UserPreferenceRepository
{
    public function save(UserPreference $preference): void;

    public function findByUserId(Uuid $userId): ?UserPreference;

    public function findByUser(User $user): ?UserPreference;
}

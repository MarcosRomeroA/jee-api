<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

interface UserRepository
{
    public function save(User $user): void;
    public function searchAll(): array;
    public function findByEmail(string $email): User;

    /**
     * @param Uuid $id
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findById(Uuid $id): User;

    public function checkIfUsernameExists(UsernameValue $username): void;
    public function checkIfEmailExists(EmailValue $email): void;

    /**
     * @param array $criteria
     * @return array<User>
     */
    public function searchByCriteria(array $criteria): array;

    /**
     * @param array $criteria
     * @return int
     */
    public function countByCriteria(array $criteria): int;

    public function findByUsername(UsernameValue $username): User;
}
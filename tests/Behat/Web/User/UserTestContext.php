<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\User;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class UserTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @user */
    public function createTestData(): void
    {
        // Crear usuarios de prueba
        $userId1 = '550e8400-e29b-41d4-a716-446655440001';
        $userId2 = '550e8400-e29b-41d4-a716-446655440002';

        // Verificar si el usuario 1 ya existe antes de persistir
        $existingUser1 = $this->entityManager->find(User::class, $userId1);
        if (!$existingUser1) {
            $user1 = User::create(
                new Uuid($userId1),
                new FirstnameValue('John'),
                new LastnameValue('Doe'),
                new UsernameValue('testuser'),
                new EmailValue('test@example.com'),
                new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
            );

            $this->entityManager->persist($user1);
        }

        // Verificar si el usuario 2 ya existe antes de persistir
        $existingUser2 = $this->entityManager->find(User::class, $userId2);
        if (!$existingUser2) {
            $user2 = User::create(
                new Uuid($userId2),
                new FirstnameValue('Jane'),
                new LastnameValue('Smith'),
                new UsernameValue('janesmith'),
                new EmailValue('jane@example.com'),
                new PasswordValue(password_hash('password456', PASSWORD_BCRYPT))
            );

            $this->entityManager->persist($user2);
        }

        $this->entityManager->flush();
    }

    /** @AfterScenario @user */
    public function cleanupTestData(): void
    {
        // Limpiar usuarios creados en los tests (excepto los de datos de prueba básicos)
        $this->entityManager->createQuery(
            'DELETE FROM App\Contexts\Web\User\Domain\User u 
             WHERE u.id NOT IN (:id1, :id2)'
        )
        ->setParameter('id1', '550e8400-e29b-41d4-a716-446655440001')
        ->setParameter('id2', '550e8400-e29b-41d4-a716-446655440002')
        ->execute();

        $this->entityManager->flush();
    }
}


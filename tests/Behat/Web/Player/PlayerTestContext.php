<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Player;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class PlayerTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @player */
    public function createTestData(): void
    {
        // Crear un usuario de prueba para los tests
        // Los juegos, roles y ranks ya existen por las migraciones
        $userId = '550e8400-e29b-41d4-a716-446655440001';

        // Verificar si el usuario ya existe antes de persistir
        $existingUser = $this->entityManager->find(User::class, $userId);
        if (!$existingUser) {
            $user = User::create(
                new Uuid($userId),
                new FirstnameValue('John'),
                new LastnameValue('Doe'),
                new UsernameValue('testuser'),
                new EmailValue('test@example.com'),
                new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /** @AfterScenario @player */
    public function cleanupTestData(): void
    {
        // Limpiar solo los players creados en los tests
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Player\Domain\Player')->execute();
        $this->entityManager->flush();
    }
}


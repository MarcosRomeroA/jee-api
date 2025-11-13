<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Post;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

final class PostTestContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario @post */
    public function createTestData(): void
    {
        // Crear un usuario de prueba
        $user = User::create(
            new Uuid('550e8400-e29b-41d4-a716-446655440001'),
            new FirstnameValue('John'),
            new LastnameValue('Doe'),
            new UsernameValue('testuser'),
            new EmailValue('test@example.com'),
            new PasswordValue(password_hash('password123', PASSWORD_BCRYPT))
        );
        $this->entityManager->persist($user);

        // Crear un post de prueba
        $post = new Post(
            new Uuid('550e8400-e29b-41d4-a716-446655440010'),
            new BodyValue('This is a test post about gaming and esports!'),
            $user,
            null // sharedPostId
        );
        $this->entityManager->persist($post);

        $this->entityManager->flush();
    }

    /** @AfterScenario @post */
    public function cleanupTestData(): void
    {
        // Limpiar posts (esto también limpiará comments, likes, dislikes por cascade)
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\Post\Domain\Post')->execute();

        // Limpiar usuarios
        $this->entityManager->createQuery('DELETE FROM App\Contexts\Web\User\Domain\User')->execute();
    }
}


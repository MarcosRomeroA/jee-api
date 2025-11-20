<?php

declare(strict_types=1);

namespace App\Tests\Behat\Web\Auth;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use App\Tests\Behat\Shared\Fixtures\TestUsers;
use Behat\Behat\Context\Context;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class AuthTestContext implements Context
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @BeforeScenario @auth */
    public function createTestData(): void
    {
        // Los usuarios estáticos (tester1, tester2, tester3) ya existen en la base de datos
        // Son creados por la migración Version20251119000001 y NO deben ser modificados
        $this->entityManager->clear();
    }

    /** @AfterScenario @auth */
    public function cleanupTestData(): void
    {
        // Los usuarios globales NO se eliminan, persisten durante toda la suite
        $this->entityManager->clear();
    }
}

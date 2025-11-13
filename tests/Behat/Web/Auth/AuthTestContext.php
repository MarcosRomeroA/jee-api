<?php declare(strict_types=1);

namespace App\Tests\Behat\Web\Auth;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Behat\Behat\Context\Context;

final class AuthTestContext implements Context
{
    private const TEST_USER_ID = '550e8400-e29b-41d4-a716-446655440099';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_PASSWORD = 'password123';

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /** @BeforeScenario @auth */
    public function createTestData(): void
    {
        // Crear usuario de prueba para login
        $user = User::create(
            new Uuid(self::TEST_USER_ID),
            new FirstnameValue('Test'),
            new LastnameValue('User'),
            new UsernameValue('testuser'),
            new EmailValue(self::TEST_EMAIL),
            new PasswordValue(password_hash(self::TEST_PASSWORD, PASSWORD_BCRYPT))
        );


        $this->userRepository->save($user);
    }

    /** @AfterScenario @auth */
    public function cleanupTestData(): void
    {
        // Limpiar usuario de prueba
        $user = $this->userRepository->findById(new Uuid(self::TEST_USER_ID));
        $this->userRepository->delete($user);
    }
}


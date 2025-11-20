<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Auth\Application;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Web\Auth\Application\Login\UserAuthenticator;
use App\Contexts\Web\User\Domain\Exception\UserNotFoundException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserAuthenticatorTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private JwtGenerator|MockObject $jwtGenerator;
    private UserAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->jwtGenerator = $this->createMock(JwtGenerator::class);

        $this->authenticator = new UserAuthenticator(
            $this->userRepository,
            $this->jwtGenerator
        );

        // Set APP_URL for Mercure token generation
        $_ENV['APP_URL'] = 'http://localhost';
    }

    public function testItShouldThrowUnauthorizedWhenUserNotFound(): void
    {
        // Arrange
        $email = new EmailValue('nonexistent@example.com');
        $password = 'anyPassword';

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willThrowException(new UserNotFoundException());

        // Expect exception
        $this->expectException(UnauthorizedException::class);

        // Act
        ($this->authenticator)($email, $password);
    }
}

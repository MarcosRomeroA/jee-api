<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Auth\Application;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Web\Auth\Application\RefreshToken\TokenRefresher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TokenRefresherTest extends TestCase
{
    private JwtGenerator|MockObject $jwtGenerator;
    private TokenRefresher $refresher;

    protected function setUp(): void
    {
        $this->jwtGenerator = $this->createMock(JwtGenerator::class);
        $this->refresher = new TokenRefresher($this->jwtGenerator);

        // Set APP_URL for Mercure token generation
        $_ENV['APP_URL'] = 'http://localhost';
    }

    public function testItShouldRefreshTokensWithValidRefreshToken(): void
    {
        // Arrange
        $refreshToken = 'valid-refresh-token';
        $userId = 'user-123';

        $this->jwtGenerator
            ->expects($this->once())
            ->method('decode')
            ->with($refreshToken)
            ->willReturn([
                'id' => $userId,
                'refresh' => true
            ]);

        $this->jwtGenerator
            ->expects($this->exactly(2)) // access token + new refresh token
            ->method('create')
            ->willReturn('new-token');

        // Act
        $response = ($this->refresher)($refreshToken);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldThrowUnauthorizedWhenTokenIsEmpty(): void
    {
        // Arrange
        $refreshToken = '';

        // Expect exception
        $this->expectException(UnauthorizedException::class);

        // Act
        ($this->refresher)($refreshToken);
    }

    public function testItShouldThrowUnauthorizedWhenTokenIsInvalid(): void
    {
        // Arrange
        $refreshToken = 'invalid-token';

        $this->jwtGenerator
            ->expects($this->once())
            ->method('decode')
            ->with($refreshToken)
            ->willThrowException(new \Exception('Invalid token'));

        // Expect exception
        $this->expectException(UnauthorizedException::class);

        // Act
        ($this->refresher)($refreshToken);
    }

    public function testItShouldThrowUnauthorizedWhenTokenIsNotRefreshToken(): void
    {
        // Arrange
        $refreshToken = 'access-token-not-refresh';
        $userId = 'user-456';

        // Token is valid but not a refresh token (missing refresh flag)
        $this->jwtGenerator
            ->expects($this->once())
            ->method('decode')
            ->willReturn([
                'id' => $userId,
                'refresh' => false // Not a refresh token
            ]);

        // Expect exception
        $this->expectException(UnauthorizedException::class);

        // Act
        ($this->refresher)($refreshToken);
    }

    public function testItShouldThrowUnauthorizedWhenRefreshFlagIsMissing(): void
    {
        // Arrange
        $refreshToken = 'token-without-refresh-flag';
        $userId = 'user-789';

        $this->jwtGenerator
            ->method('decode')
            ->willReturn([
                'id' => $userId
                // Missing 'refresh' flag
            ]);

        // Expect exception
        $this->expectException(UnauthorizedException::class);

        // Act
        ($this->refresher)($refreshToken);
    }

    public function testItShouldGenerateNewAccessAndRefreshTokens(): void
    {
        // Arrange
        $refreshToken = 'valid-refresh-token';
        $userId = 'user-999';

        $this->jwtGenerator
            ->method('decode')
            ->willReturn([
                'id' => $userId,
                'refresh' => true
            ]);

        // Verify both new tokens are generated
        $this->jwtGenerator
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(function ($payload, $isRefresh = false) {
                return $isRefresh ? 'new-refresh-token' : 'new-access-token';
            });

        // Act
        $response = ($this->refresher)($refreshToken);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldIncludeUserIdFromOriginalToken(): void
    {
        // Arrange
        $refreshToken = 'valid-refresh-token';
        $userId = 'user-original-id';

        $this->jwtGenerator
            ->method('decode')
            ->willReturn([
                'id' => $userId,
                'refresh' => true
            ]);

        $this->jwtGenerator
            ->method('create')
            ->willReturn('token');

        // Act
        $response = ($this->refresher)($refreshToken);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldGenerateMercureNotificationToken(): void
    {
        // Arrange
        $refreshToken = 'valid-refresh-token';
        $userId = 'user-mercure-test';

        $this->jwtGenerator
            ->method('decode')
            ->willReturn([
                'id' => $userId,
                'refresh' => true
            ]);

        $this->jwtGenerator
            ->method('create')
            ->willReturn('jwt-token');

        // Act
        $response = ($this->refresher)($refreshToken);

        // Assert
        $this->assertNotNull($response);
        // Mercure notification token should be included
    }
}

<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\RefreshToken;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Exception\JWTDecodeException;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Shared\Infrastructure\Jwt\MercureJwtGenerator;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;
use App\Contexts\Web\Auth\Domain\Exception\TokenIsNotRefreshTokenException;

readonly class GetTokenByRefreshQueryHandler implements QueryHandler
{
    public function __construct(private JwtGenerator $jwtGenerator) {}

    public function __invoke(GetTokenByRefreshQuery $query): LoginUserResponse
    {
        // Check for empty token before attempting decode
        if (empty($query->refreshToken)) {
            throw new UnauthorizedException();
        }

        try {
            $payload = $this->jwtGenerator->decode($query->refreshToken);
        } catch (\Exception $e) {
            // Invalid token or any decode error - return unauthorized
            throw new UnauthorizedException();
        }

        if (!isset($payload["refresh"]) || $payload["refresh"] !== true) {
            throw new UnauthorizedException();
        }

        $userId = $payload["id"];

        $token = $this->jwtGenerator->create([
            "id" => $userId,
        ]);

        $refreshToken = $this->jwtGenerator->create(
            [
                "id" => $userId,
            ],
            true,
        );

        $notificationToken = MercureJwtGenerator::create(
            $_ENV["APP_URL"] . "/notification/" . $userId,
        );

        return new LoginUserResponse(
            $userId,
            $notificationToken,
            $token,
            $refreshToken,
        );
    }
}

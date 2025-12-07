<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\RefreshToken;

use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Jwt\MercureJwtGenerator;
use App\Contexts\Web\Auth\Application\Shared\LoginUserResponse;
use App\Contexts\Web\User\Application\Shared\UserPreferenceResponse;
use App\Contexts\Web\User\Domain\UserPreferenceRepository;

final readonly class TokenRefresher
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
        private UserPreferenceRepository $preferenceRepository,
    ) {
    }

    public function __invoke(string $refreshToken): LoginUserResponse
    {
        $this->ensureTokenIsNotEmpty($refreshToken);

        $payload = $this->decodeToken($refreshToken);

        $this->ensureIsRefreshToken($payload);

        $userId = $payload["id"];

        return $this->generateTokens($userId);
    }

    private function ensureTokenIsNotEmpty(string $refreshToken): void
    {
        if (empty($refreshToken)) {
            throw new UnauthorizedException();
        }
    }

    private function decodeToken(string $refreshToken): array
    {
        try {
            return $this->jwtGenerator->decode($refreshToken);
        } catch (\Exception $e) {
            throw new UnauthorizedException();
        }
    }

    private function ensureIsRefreshToken(array $payload): void
    {
        if (!isset($payload["refresh"]) || $payload["refresh"] !== true) {
            throw new UnauthorizedException();
        }
    }

    private function generateTokens(string $userId): LoginUserResponse
    {
        $token = $this->jwtGenerator->create([
            "id" => $userId,
        ]);

        $refreshToken = $this->jwtGenerator->create(
            [
                "id" => $userId,
            ],
            true
        );

        $notificationToken = MercureJwtGenerator::create(
            $_ENV["APP_URL"] . "/notification/" . $userId
        );

        $preference = $this->preferenceRepository->findByUserId(new Uuid($userId));
        $preferenceResponse = $preference !== null
            ? UserPreferenceResponse::fromEntity($preference)
            : UserPreferenceResponse::default();

        return new LoginUserResponse(
            $userId,
            $notificationToken,
            $token,
            $refreshToken,
            $preferenceResponse,
        );
    }
}

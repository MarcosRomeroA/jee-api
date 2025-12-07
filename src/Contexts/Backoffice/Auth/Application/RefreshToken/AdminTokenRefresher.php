<?php declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\RefreshToken;

use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Auth\Application\Shared\LoginAdminResponse;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class AdminTokenRefresher
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
        private AdminRepository $adminRepository,
    ) {
    }

    public function __invoke(string $refreshToken): LoginAdminResponse
    {
        $this->ensureTokenIsNotEmpty($refreshToken);

        $payload = $this->decodeToken($refreshToken);

        $this->ensureIsRefreshToken($payload);
        $this->ensureIsAdminToken($payload);

        $adminId = $payload['id'];

        return $this->generateTokens($adminId);
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
        if (!isset($payload['refresh']) || $payload['refresh'] !== true) {
            throw new UnauthorizedException();
        }
    }

    private function ensureIsAdminToken(array $payload): void
    {
        if (!isset($payload['type']) || $payload['type'] !== 'admin') {
            throw new UnauthorizedException();
        }
    }

    private function generateTokens(string $adminId): LoginAdminResponse
    {
        $admin = $this->adminRepository->findById(new Uuid($adminId));

        if ($admin === null || $admin->isDeleted()) {
            throw new UnauthorizedException();
        }

        $token = $this->jwtGenerator->create([
            'id' => $admin->getId()->value(),
            'type' => 'admin',
            'role' => $admin->getRole()->value,
        ]);

        $refreshToken = $this->jwtGenerator->create(
            [
                'id' => $admin->getId()->value(),
                'type' => 'admin',
                'role' => $admin->getRole()->value,
            ],
            true,
        );

        return new LoginAdminResponse(
            $admin->getId()->value(),
            $token,
            $refreshToken,
            $admin->getRole()->value,
        );
    }
}

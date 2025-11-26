<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Auth\Application\Login;

use App\Contexts\Backoffice\Admin\Domain\AdminRepository;
use App\Contexts\Backoffice\Admin\Domain\Exception\AdminNotFoundException;
use App\Contexts\Backoffice\Admin\Domain\ValueObject\AdminUserValue;
use App\Contexts\Backoffice\Auth\Application\Shared\LoginAdminResponse;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;

final readonly class AdminAuthenticator
{
    public function __construct(
        private AdminRepository $adminRepository,
        private JwtGenerator $jwtGenerator,
    ) {
    }

    public function __invoke(
        AdminUserValue $user,
        string $password,
    ): LoginAdminResponse {
        $admin = $this->adminRepository->findByUser($user);

        if (!$admin) {
            throw new UnauthorizedException();
        }

        // Check if admin is deleted
        if ($admin->isDeleted()) {
            throw new UnauthorizedException();
        }

        if (!$admin->getPassword()->verifyPassword($password)) {
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

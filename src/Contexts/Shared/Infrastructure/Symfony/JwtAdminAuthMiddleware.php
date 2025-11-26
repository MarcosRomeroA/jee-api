<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ExpiredTokenException;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class JwtAdminAuthMiddleware
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $shouldAuthenticateAdmin = $request->attributes->get('auth_admin', false);
        $shouldAuthenticateSuperAdmin = $request->attributes->get('auth_superadmin', false);

        if (!$shouldAuthenticateAdmin && !$shouldAuthenticateSuperAdmin) {
            return;
        }

        $jwtToken = $request->headers->get('Authorization');

        if (!$jwtToken) {
            throw new UnauthorizedException();
        }

        $payload = $this->verify($jwtToken);

        // Verify that the token is for an admin user
        if (!isset($payload['type']) || $payload['type'] !== 'admin') {
            throw new UnauthorizedException();
        }

        // If route requires superadmin, verify the role
        if ($shouldAuthenticateSuperAdmin) {
            if (!isset($payload['role']) || $payload['role'] !== 'superadmin') {
                throw new UnauthorizedException();
            }
        }

        $request->attributes->set('sessionId', $payload['id']);
        $request->attributes->set('sessionType', 'admin');
    }

    private function verify(string $bearer): array
    {
        try {
            $this->jwtGenerator->verify($bearer);
        } catch (\Exception $e) {
            throw new ExpiredTokenException();
        }

        return $this->jwtGenerator->decode($bearer);
    }
}

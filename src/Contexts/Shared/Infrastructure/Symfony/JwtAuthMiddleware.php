<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ExpiredTokenException;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class JwtAuthMiddleware
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
        private UserRepository $userRepository,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $shouldAuthenticate = $event->getRequest()->attributes->get('auth', false);

        $jwtToken = $event->getRequest()->headers->get('Authorization');

        if (!$shouldAuthenticate) {
            return;
        }

        if (!$jwtToken) {
            throw new UnauthorizedException();
        }

        $payload = $this->verify($jwtToken);

        // Verificar si el usuario está deshabilitado
        $userId = new Uuid($payload['id']);
        $user = $this->userRepository->findById($userId);

        if (!$user || $user->isDisabled()) {
            throw new UnauthorizedException();
        }

        $request->attributes->set('sessionId', $payload['id']);
    }

    public function verify(string $bearer): array
    {
        try {
            $this->jwtGenerator->verify($bearer);

        } catch (\Exception $e) {
            throw new ExpiredTokenException();
        }

        return $this->jwtGenerator->decode($bearer);
    }
}

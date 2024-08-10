<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ExpiredTokenException;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class JwtAuthMiddleware
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
    )
    {
    }


    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $shouldAuthenticate = $event->getRequest()->attributes->get('auth', false);

        $jwtToken = $event->getRequest()->headers->get('Authorization');

        if (!$shouldAuthenticate)
            return;

        if (!$jwtToken)
            throw new UnauthorizedException();

        $payload = $this->verify($jwtToken);

        $request->attributes->set('sessionId', $payload['id']);
    }

    public function verify(string $bearer): array
    {
        $this->jwtGenerator->verify($bearer);

        $payload = $this->jwtGenerator->decode($bearer);

        if(isset($payload['exp']) && isset($payload['iat']) )
        {
            if( $payload['exp'] <= time() )
            {
                throw new ExpiredTokenException();
            }
        }

        return $payload;
    }
}
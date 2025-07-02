<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Jwt;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureJwtGenerator
{
    public static function create(string $subscribe): string{
        $key = InMemory::plainText($_ENV['MERCURE_JWT_SECRET']);

        $token = (new JwtFacade())->issue(
            new Sha256(),
            $key,
            static fn (
                Builder $builder,
                \DateTimeImmutable $issuedAt
            ): Builder => $builder
                ->withClaim('mercure', [
                    'subscribe' => [$subscribe],
                ])
                ->expiresAt($issuedAt->modify('+120 minutes'))
        );

        return $token->toString();
    }
}
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Jwt;

interface JwtGenerator
{
    public function create(array $body, bool $isRefreshToken = false): string;

    public function verify(string $token) : void;

    public function ttl(string $token) : int;

    public function decode(string $bearer): array;
}
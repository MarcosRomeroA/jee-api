<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Jwt;

use App\Contexts\Shared\Domain\Exception\JWTDecodeException;
use App\Contexts\Shared\Domain\Exception\JWTEncodeException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

readonly class LexikJwtGenerator implements JwtGenerator
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
    )
    {
    }

    public function create(array $body, bool $isRefreshToken = false): string
    {
        try {
            $body['exp'] = time() + $_ENV['JWT_TTL'];

            if ($isRefreshToken) {
                $body['exp'] = time() + $_ENV['JWT_REFRESH_TTL'];
                $body['refresh'] = true;
            }

            return $this->jwtEncoder->encode($body);
        }
        catch (\Exception)
        {
            throw new JWTEncodeException();
        }
    }

    /**
     * @throws JWTDecodeFailureException
     */
    public function verify(string $token): void
    {
        $this->jwtEncoder->decode(str_replace('Bearer ','',$token));
    }


    /**
     * @throws JWTDecodeFailureException
     */
    public function ttl(string $token) : int
    {
        $decoded = $this->jwtEncoder->decode($token);

        return $decoded['exp'] - $decoded['iat'];
    }

    public function decode(string $bearer): array
    {
        try
        {
            return $this->jwtEncoder->decode(str_replace('Bearer ','',$bearer));
        }
        catch(\Exception $e)
        {
            throw new JWTDecodeException($e->getMessage());
        }
    }

    public function createWithSecret(array $body): string
    {
        return $this->jwtEncoder->encode($body);
    }
}
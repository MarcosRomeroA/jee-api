<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Refresh;

use App\Contexts\Web\Auth\Application\RefreshToken\GetTokenByRefreshQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetTokenByRefreshRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $refreshToken,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['refreshToken'] ?? ''
        );
    }

    public function toQuery(): GetTokenByRefreshQuery
    {
        return new GetTokenByRefreshQuery($this->refreshToken);
    }
}


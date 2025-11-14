<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Refresh;

use App\Contexts\Shared\Infrastructure\Symfony\Exception\ValidationException;
use App\Contexts\Web\Auth\Application\RefreshToken\GetTokenByRefreshQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetTokenByRefreshRequest
{
    public function __construct(
        #[Assert\NotBlank(message: "The refreshToken field is required")] #[
            Assert\Type("string"),
        ]
        public string $refreshToken,
    ) {}

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            $data = [];
        }

        $refreshToken = $data["refreshToken"] ?? null;

        if ($refreshToken === null || $refreshToken === "") {
            throw new ValidationException([
                "refreshToken" => ["The refreshToken field is required"],
            ]);
        }

        return new self($refreshToken);
    }

    public function toQuery(): GetTokenByRefreshQuery
    {
        return new GetTokenByRefreshQuery($this->refreshToken);
    }
}

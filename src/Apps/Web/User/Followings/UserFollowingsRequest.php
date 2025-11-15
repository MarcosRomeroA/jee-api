<?php declare(strict_types=1);

namespace App\Apps\Web\User\Followings;

use App\Contexts\Web\User\Application\Followings\UserFollowingsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UserFollowingsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        public string $sessionId,
        #[Assert\Type("int")]
        public ?int $limit = null,
        #[Assert\Type("int")]
        public ?int $offset = null,
    ) {}

    public static function fromHttp(
        string $id,
        string $sessionId,
        Request $request
    ): self {
        return new self(
            $id,
            $sessionId,
            $request->query->get("limit")
                ? (int) $request->query->get("limit")
                : null,
            $request->query->get("offset")
                ? (int) $request->query->get("offset")
                : null,
        );
    }

    public function toQuery(): UserFollowingsQuery
    {
        return new UserFollowingsQuery(
            $this->id,
            $this->sessionId,
            $this->limit,
            $this->offset
        );
    }
}

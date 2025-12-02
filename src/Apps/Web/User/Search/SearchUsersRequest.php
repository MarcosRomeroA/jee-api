<?php

declare(strict_types=1);

namespace App\Apps\Web\User\Search;

use App\Contexts\Web\User\Application\Search\SearchUsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUsersRequest
{
    public function __construct(
        #[Assert\Type("string")]
        public ?string $firstname = null,
        #[Assert\Type("string")]
        public ?string $lastname = null,
        #[Assert\Type("string")]
        public ?string $username = null,
        #[Assert\Type("string")]
        public ?string $email = null,
        #[Assert\Type("string")]
        public ?string $gameId = null,
        #[Assert\Type("string")]
        public ?string $gameRankId = null,
        #[Assert\Type("string")]
        public ?string $gameRoleId = null,
        #[Assert\Type("int")]
        public ?int $limit = null,
        #[Assert\Type("int")]
        public ?int $offset = null,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        return new self(
            firstname: $request->query->get("firstname"),
            lastname: $request->query->get("lastname"),
            username: $request->query->get("username"),
            email: $request->query->get("email"),
            gameId: $request->query->get("gameId"),
            gameRankId: $request->query->get("gameRankId"),
            gameRoleId: $request->query->get("gameRoleId"),
            limit: $request->query->get("limit")
                ? (int) $request->query->get("limit")
                : null,
            offset: $request->query->get("offset")
                ? (int) $request->query->get("offset")
                : null,
        );
    }

    public function toQuery(): SearchUsersQuery
    {
        $criteria = [];

        if ($this->firstname !== null) {
            $criteria["firstname"] = $this->firstname;
        }

        if ($this->lastname !== null) {
            $criteria["lastname"] = $this->lastname;
        }

        if ($this->username !== null) {
            $criteria["username"] = $this->username;
        }

        if ($this->email !== null) {
            $criteria["email"] = $this->email;
        }

        if ($this->gameId !== null) {
            $criteria["gameId"] = $this->gameId;
        }

        if ($this->gameRankId !== null) {
            $criteria["gameRankId"] = $this->gameRankId;
        }

        if ($this->gameRoleId !== null) {
            $criteria["gameRoleId"] = $this->gameRoleId;
        }

        if ($this->limit !== null) {
            $criteria["limit"] = $this->limit;
        }

        if ($this->offset !== null) {
            $criteria["offset"] = $this->offset;
        }

        return new SearchUsersQuery($criteria);
    }
}

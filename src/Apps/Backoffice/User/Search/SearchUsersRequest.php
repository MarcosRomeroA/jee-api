<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\User\Search;

use App\Contexts\Backoffice\User\Application\Search\SearchUsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchUsersRequest
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $email = null,
        #[Assert\Type('string')]
        public ?string $username = null,
        #[Assert\Type('bool')]
        public ?bool $verified = null,
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public int $limit = 20,
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public int $offset = 0,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $email = $request->query->get('email');
        $username = $request->query->get('username');
        $verified = $request->query->get('verified');
        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);

        // Convert verified string to bool if present
        $verifiedBool = null;
        if ($verified !== null) {
            $verifiedBool = filter_var($verified, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return new self(
            email: $email,
            username: $username,
            verified: $verifiedBool,
            limit: min($limit, 100), // Max 100 results
            offset: $offset,
        );
    }

    public function toQuery(): SearchUsersQuery
    {
        $criteria = [];

        if ($this->email !== null) {
            $criteria['email'] = $this->email;
        }

        if ($this->username !== null) {
            $criteria['username'] = $this->username;
        }

        if ($this->verified !== null) {
            $criteria['verified'] = $this->verified;
        }

        $criteria['limit'] = $this->limit;
        $criteria['offset'] = $this->offset;

        return new SearchUsersQuery($criteria);
    }
}

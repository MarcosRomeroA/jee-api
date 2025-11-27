<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Post\Search;

use App\Contexts\Backoffice\Post\Application\Search\SearchPostsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsRequest
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $postId = null,
        #[Assert\Type('string')]
        public ?string $userId = null,
        #[Assert\Type('string')]
        public ?string $username = null,
        #[Assert\Type('string')]
        public ?string $email = null,
        #[Assert\Type('string')]
        public ?string $q = null,
        #[Assert\Type('bool')]
        public ?bool $disabled = null,
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
        $disabled = $request->query->get('disabled');
        $disabledBool = null;
        if ($disabled !== null) {
            $disabledBool = filter_var($disabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return new self(
            postId: $request->query->get('postId'),
            userId: $request->query->get('userId'),
            username: $request->query->get('username'),
            email: $request->query->get('email'),
            q: $request->query->get('q'),
            disabled: $disabledBool,
            limit: min($request->query->getInt('limit', 20), 100),
            offset: $request->query->getInt('offset', 0),
        );
    }

    public function toQuery(): SearchPostsQuery
    {
        $criteria = [
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        if ($this->postId !== null) {
            $criteria['postId'] = $this->postId;
        }

        if ($this->userId !== null) {
            $criteria['userId'] = $this->userId;
        }

        if ($this->username !== null) {
            $criteria['username'] = $this->username;
        }

        if ($this->email !== null) {
            $criteria['email'] = $this->email;
        }

        if ($this->q !== null) {
            $criteria['q'] = $this->q;
        }

        if ($this->disabled !== null) {
            $criteria['disabled'] = $this->disabled;
        }

        return new SearchPostsQuery($criteria);
    }
}

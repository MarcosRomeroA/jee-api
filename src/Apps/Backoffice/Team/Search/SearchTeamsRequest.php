<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Team\Search;

use App\Contexts\Backoffice\Team\Application\Search\SearchTeamsQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchTeamsRequest
{
    public function __construct(
        #[Assert\Type('string')]
        public ?string $teamId = null,
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\Type('string')]
        public ?string $creatorUsername = null,
        #[Assert\Type('string')]
        public ?string $creatorEmail = null,
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
            teamId: $request->query->get('teamId'),
            name: $request->query->get('name'),
            creatorUsername: $request->query->get('creatorUsername'),
            creatorEmail: $request->query->get('creatorEmail'),
            disabled: $disabledBool,
            limit: min($request->query->getInt('limit', 20), 100),
            offset: $request->query->getInt('offset', 0),
        );
    }

    public function toQuery(): SearchTeamsQuery
    {
        $criteria = [
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        if ($this->teamId !== null) {
            $criteria['teamId'] = $this->teamId;
        }

        if ($this->name !== null) {
            $criteria['name'] = $this->name;
        }

        if ($this->creatorUsername !== null) {
            $criteria['creatorUsername'] = $this->creatorUsername;
        }

        if ($this->creatorEmail !== null) {
            $criteria['creatorEmail'] = $this->creatorEmail;
        }

        if ($this->disabled !== null) {
            $criteria['disabled'] = $this->disabled;
        }

        return new SearchTeamsQuery($criteria);
    }
}

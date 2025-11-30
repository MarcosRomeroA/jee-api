<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Tournament\Search;

use App\Contexts\Backoffice\Tournament\Application\Search\SearchTournamentsQuery;
use Symfony\Component\HttpFoundation\Request;

final readonly class SearchTournamentsRequest
{
    public function __construct(
        public ?string $tournamentId,
        public ?string $name,
        public ?string $responsibleUsername,
        public ?string $responsibleEmail,
        public ?bool $disabled,
        public int $limit,
        public int $offset,
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $disabled = $request->query->get('disabled');

        return new self(
            tournamentId: $request->query->get('tournamentId'),
            name: $request->query->get('name'),
            responsibleUsername: $request->query->get('responsibleUsername'),
            responsibleEmail: $request->query->get('responsibleEmail'),
            disabled: $disabled !== null ? filter_var($disabled, FILTER_VALIDATE_BOOLEAN) : null,
            limit: (int) $request->query->get('limit', 20),
            offset: (int) $request->query->get('offset', 0),
        );
    }

    public function toQuery(): SearchTournamentsQuery
    {
        $criteria = [
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        if ($this->tournamentId !== null) {
            $criteria['tournamentId'] = $this->tournamentId;
        }

        if ($this->name !== null) {
            $criteria['name'] = $this->name;
        }

        if ($this->responsibleUsername !== null) {
            $criteria['responsibleUsername'] = $this->responsibleUsername;
        }

        if ($this->responsibleEmail !== null) {
            $criteria['responsibleEmail'] = $this->responsibleEmail;
        }

        if ($this->disabled !== null) {
            $criteria['disabled'] = $this->disabled;
        }

        return new SearchTournamentsQuery($criteria);
    }
}

<?php declare(strict_types=1);

namespace App\Apps\Web\Team\SearchRosters;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use App\Contexts\Web\Team\Application\SearchRosters\SearchRostersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class SearchRostersRequest extends BaseRequest
{
    public ?array $q;

    public function __construct(
        ValidatorInterface $validator,
        RequestStack $requestStack
    ) {
        parent::__construct($validator, $requestStack);
    }

    public static function fromRequest(Request $request, ValidatorInterface $validator, RequestStack $requestStack): self
    {
        return new self($validator, $requestStack);
    }

    public function toQuery(string $teamId): SearchRostersQuery
    {
        $limit = $this->q['limit'] ?? 10;
        $offset = $this->q['offset'] ?? 0;

        return new SearchRostersQuery(
            $teamId,
            (int) $limit,
            (int) $offset
        );
    }
}


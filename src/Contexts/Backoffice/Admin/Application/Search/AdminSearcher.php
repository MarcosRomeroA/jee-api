<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Search;

use App\Contexts\Backoffice\Admin\Application\Shared\AdminCollectionResponse;
use App\Contexts\Backoffice\Admin\Application\Shared\AdminResponse;
use App\Contexts\Backoffice\Admin\Domain\AdminRepository;

final readonly class AdminSearcher
{
    public function __construct(
        private AdminRepository $repository
    ) {
    }

    public function __invoke(array $criteria): AdminCollectionResponse
    {
        $admins = $this->repository->searchByCriteria($criteria);

        $response = [];
        foreach ($admins as $admin) {
            $response[] = AdminResponse::fromEntity($admin);
        }

        $total = $this->repository->countByCriteria($criteria);

        return new AdminCollectionResponse(
            $response,
            $criteria,
            $total
        );
    }
}

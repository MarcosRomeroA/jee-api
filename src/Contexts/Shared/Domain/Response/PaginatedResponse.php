<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Response;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class PaginatedResponse extends Response
{
    private function __construct(
        private readonly array $data,
        private readonly int $currentPage,
        private readonly int $totalItems,
        private readonly int $itemsPerPage
    ) {}

    public static function create(
        array $data,
        int $currentPage,
        int $totalItems,
        int $itemsPerPage
    ): self {
        return new self($data, $currentPage, $totalItems, $itemsPerPage);
    }

    public function data(): array
    {
        return $this->data;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }

    public function itemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function totalPages(): int
    {
        if ($this->itemsPerPage === 0) {
            return 0;
        }

        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function toArray(): array
    {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;

        return [
            'data' => $this->data,
            'metadata' => [
                'limit' => $this->itemsPerPage,
                'offset' => $offset,
                'total' => $this->totalItems,
                'count' => count($this->data),
            ],
        ];
    }
}


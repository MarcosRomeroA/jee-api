<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Response;

final readonly class PaginatedResponse
{
    private function __construct(
        private array $data,
        private int $currentPage,
        private int $totalItems,
        private int $itemsPerPage
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

    public function totalPages(): int
    {
        if ($this->itemsPerPage === 0) {
            return 0;
        }

        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }

    public function itemsPerPage(): int
    {
        return $this->itemsPerPage;
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
        return [
            'data' => $this->data,
            'pagination' => [
                'currentPage' => $this->currentPage,
                'totalPages' => $this->totalPages(),
                'totalItems' => $this->totalItems,
                'itemsPerPage' => $this->itemsPerPage,
                'hasNextPage' => $this->hasNextPage(),
                'hasPreviousPage' => $this->hasPreviousPage(),
            ],
        ];
    }
}


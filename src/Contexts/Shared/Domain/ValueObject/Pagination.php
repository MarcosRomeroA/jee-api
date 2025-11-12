<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

final readonly class Pagination
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_LIMIT = 20;
    private const MAX_LIMIT = 100;

    private function __construct(
        private int $page,
        private int $limit
    ) {
        $this->ensurePageIsValid();
        $this->ensureLimitIsValid();
    }

    public static function create(int $page, int $limit): self
    {
        return new self($page, $limit);
    }

    public static function fromRequest(?int $page, ?int $limit): self
    {
        return new self(
            $page ?? self::DEFAULT_PAGE,
            $limit ?? self::DEFAULT_LIMIT
        );
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    private function ensurePageIsValid(): void
    {
        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }
    }

    private function ensureLimitIsValid(): void
    {
        if ($this->limit < 1 || $this->limit > self::MAX_LIMIT) {
            throw new \InvalidArgumentException(
                sprintf('Limit must be between 1 and %d', self::MAX_LIMIT)
            );
        }
    }
}


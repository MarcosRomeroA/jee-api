<?php declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\Pagination;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    /** @test */
    public function it_should_create_pagination_with_valid_values(): void
    {
        $pagination = Pagination::create(1, 20);

        $this->assertEquals(1, $pagination->page());
        $this->assertEquals(20, $pagination->limit());
        $this->assertEquals(0, $pagination->offset());
    }

    /** @test */
    public function it_should_calculate_correct_offset(): void
    {
        $pagination = Pagination::create(3, 20);

        $this->assertEquals(40, $pagination->offset());
    }

    /** @test */
    public function it_should_create_from_request_with_null_values(): void
    {
        $pagination = Pagination::fromRequest(null, null);

        $this->assertEquals(1, $pagination->page());
        $this->assertEquals(20, $pagination->limit());
    }

    /** @test */
    public function it_should_fail_with_page_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Page must be greater than 0');

        Pagination::create(0, 20);
    }

    /** @test */
    public function it_should_fail_with_negative_page(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Pagination::create(-1, 20);
    }

    /** @test */
    public function it_should_fail_with_limit_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Pagination::create(1, 0);
    }

    /** @test */
    public function it_should_fail_with_limit_exceeding_maximum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit must be between 1 and 100');

        Pagination::create(1, 101);
    }
}


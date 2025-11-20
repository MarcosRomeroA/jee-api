<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Application;

use App\Contexts\Web\Post\Application\SearchByPopularHashtag\SearchPostsByPopularHashtagQuery;
use App\Contexts\Web\Post\Application\SearchByPopularHashtag\SearchPostsByPopularHashtagQueryHandler;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Tests\Unit\Web\Post\Domain\PostMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SearchPostsByPopularHashtagQueryHandlerTest extends TestCase
{
    private PostRepository|MockObject $repository;
    private SearchPostsByPopularHashtagQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PostRepository::class);
        $this->handler = new SearchPostsByPopularHashtagQueryHandler($this->repository);
    }

    public function testItShouldSearchPostsByPopularHashtag(): void
    {
        // Arrange
        $hashtag = 'testing';
        $days = 30;
        $page = 1;
        $limit = 10;
        $offset = 0;

        $post1 = PostMother::withBody('First post with #testing hashtag');
        $post2 = PostMother::withBody('Second post with #testing and #php');

        $posts = [$post1, $post2];
        $totalCount = 2;

        $this->repository
            ->expects($this->once())
            ->method('findByPopularHashtag')
            ->with($hashtag, $days, $limit, $offset)
            ->willReturn($posts);

        $this->repository
            ->expects($this->once())
            ->method('countByPopularHashtag')
            ->with($hashtag, $days)
            ->willReturn($totalCount);

        $query = new SearchPostsByPopularHashtagQuery($hashtag, $page, $limit, $days);

        // Act
        $response = ($this->handler)($query);

        // Assert
        $responseArray = $response->toArray();

        $this->assertArrayHasKey('data', $responseArray);
        $this->assertArrayHasKey('metadata', $responseArray);
        $this->assertCount(2, $responseArray['data']);
        $this->assertEquals(2, $responseArray['metadata']['total']);
        $this->assertEquals(10, $responseArray['metadata']['limit']);
        $this->assertEquals(0, $responseArray['metadata']['offset']);
    }

    public function testItShouldReturnEmptyResultWhenNoPostsFound(): void
    {
        // Arrange
        $hashtag = 'nonexistent';
        $days = 30;
        $page = 1;
        $limit = 10;
        $offset = 0;

        $this->repository
            ->expects($this->once())
            ->method('findByPopularHashtag')
            ->with($hashtag, $days, $limit, $offset)
            ->willReturn([]);

        $this->repository
            ->expects($this->once())
            ->method('countByPopularHashtag')
            ->with($hashtag, $days)
            ->willReturn(0);

        $query = new SearchPostsByPopularHashtagQuery($hashtag, $page, $limit, $days);

        // Act
        $response = ($this->handler)($query);

        // Assert
        $responseArray = $response->toArray();

        $this->assertCount(0, $responseArray['data']);
        $this->assertEquals(0, $responseArray['metadata']['total']);
    }

    public function testItShouldSearchWithCustomDaysParameter(): void
    {
        // Arrange
        $hashtag = 'gaming';
        $days = 7; // Last week only
        $page = 1;
        $limit = 20;
        $offset = 0;

        $post = PostMother::withBody('Recent post about #gaming');
        $posts = [$post];

        $this->repository
            ->expects($this->once())
            ->method('findByPopularHashtag')
            ->with($hashtag, $days, $limit, $offset)
            ->willReturn($posts);

        $this->repository
            ->expects($this->once())
            ->method('countByPopularHashtag')
            ->with($hashtag, $days)
            ->willReturn(1);

        $query = new SearchPostsByPopularHashtagQuery($hashtag, $page, $limit, $days);

        // Act
        $response = ($this->handler)($query);

        // Assert
        $responseArray = $response->toArray();

        $this->assertCount(1, $responseArray['data']);
        $this->assertEquals(1, $responseArray['metadata']['total']);
        $this->assertEquals(20, $responseArray['metadata']['limit']);
    }

    public function testItShouldHandlePagination(): void
    {
        // Arrange
        $hashtag = 'symfony';
        $days = 30;
        $page = 2;
        $limit = 5;
        $offset = 5; // (page-1) * limit

        $posts = [
            PostMother::withBody('Post 6 #symfony'),
            PostMother::withBody('Post 7 #symfony'),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findByPopularHashtag')
            ->with($hashtag, $days, $limit, $offset)
            ->willReturn($posts);

        $this->repository
            ->expects($this->once())
            ->method('countByPopularHashtag')
            ->with($hashtag, $days)
            ->willReturn(12); // Total posts across all pages

        $query = new SearchPostsByPopularHashtagQuery($hashtag, $page, $limit, $days);

        // Act
        $response = ($this->handler)($query);

        // Assert
        $responseArray = $response->toArray();

        $this->assertCount(2, $responseArray['data']);
        $this->assertEquals(12, $responseArray['metadata']['total']);
        $this->assertEquals(5, $responseArray['metadata']['limit']);
        $this->assertEquals(5, $responseArray['metadata']['offset']);
    }
}

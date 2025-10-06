<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

class PostCollectionResponse extends Response
{
    /** @var Post[] */
    public array $posts;
    public int $limit;
    public int $offset;
    public int $total;

    /**
     * @param array<Post> $posts
     * @param array{limit: int, offset: int} $criteria
     * @param int $total
     */
    public function __construct(array $posts, array $criteria, int $total = 0)
    {
        $this->posts = $posts;
        $this->limit = $criteria["limit"];
        $this->offset = $criteria["offset"];
        $this->total = $total;
    }

    public function toArray(): array
    {
        $response = [];

        foreach($this->posts as $post){
            $response[] = PostResponse::fromEntity($post, true)->toArray();
        }

        return [
            'data' => $response,
            'metadata' => [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'total' => $this->total,
                'count' => count($this->posts)
            ]
        ];
    }
}
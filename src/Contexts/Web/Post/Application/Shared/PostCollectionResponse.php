<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

class PostCollectionResponse extends Response
{
    /**
     * @param array<Post> $posts
     */
    public function __construct(private readonly array $posts)
    {
    }

    public function toArray(): array
    {
        $response = [];

        foreach($this->posts as $post){
            $response[] = PostResponse::fromEntity($post);
        }

        return $response;
    }
}
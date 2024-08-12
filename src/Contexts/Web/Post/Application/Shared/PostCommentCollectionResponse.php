<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Comment;

class PostCommentCollectionResponse extends Response
{
    /**
     * @param array<Comment> $comments
     */
    public function __construct(private readonly array $comments)
    {
    }

    public function toArray(): array
    {
        $response = [];

        foreach($this->comments as $comment){
            $response[] = PostCommentResponse::fromEntity($comment);
        }

        return $response;
    }
}